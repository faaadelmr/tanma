<x-app-layout>
    <x-slot name="header">
        <h2 class="text-primary font-semibold text-2xl leading-tight">
            {{ __('Rapat Mingguan') }}
        </h2>
    </x-slot>


    <div class="py-12 bg-base-200 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-4xl font-bold text-primary"></h1>
                <form action="{{ route('meetings.generate') }}" method="POST" class="inline">
                    @csrf
                    <button type="button" onclick="generateMeetings()" class="btn btn-secondary gap-2">
                        <i class="fa-solid fa-calendar-plus"></i>
                        Generate Meetings
                    </button>
                </form>
            </div>


            @foreach ($meetings as $meeting)
                <div class="card bg-base-100 shadow-xl mb-6 hover:shadow-2xl transition-shadow duration-300">
                    <div class="card-body">
                        <div class="flex justify-between items-center border-b pb-4">
                            <div class="flex items-center gap-3">
                                <div class="badge badge-primary p-3">
                                    <i class="fa-solid fa-calendar-day text-lg"></i>
                                </div>
                                <h2 class="card-title text-2xl">
                                    {{ $meeting->meeting_date->isoFormat('dddd, D MMMM Y') }}
                                </h2>
                            </div>
                            <button onclick="openTopicModal({{ $meeting->id }})" class="btn btn-primary gap-2">
                                <i class="fa-solid fa-plus"></i>
                                New Topic
                            </button>
                        </div>

                        <div class="space-y-4 mt-4">
                            @forelse($meeting->topics as $topic)
                                <div
                                    class="card bg-base-100 border-2 {{ $topic->is_completed ? 'border-success/30 bg-success/5' : 'border-base-300' }} hover:border-primary/50 transition-colors">
                                    <div class="card-body">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                <button onclick="toggleComplete({{ $topic->id }})"
                                                    class="btn btn-circle btn-sm {{ $topic->is_completed ? 'btn-success' : 'btn-warning' }}">
                                                    <i
                                                        class="fa-solid {{ $topic->is_completed ? 'fa-check' : 'fa-clock' }} text-lg"></i>
                                                </button>
                                                <div>
                                                    <h3 class="text-lg font-semibold {{ $topic->is_completed ? 'line-through opacity-50' : '' }}">
                                                        {{ $topic->title }}
                                                        <span class="text-sm text-base-content/70 ml-2">
                                                            <i class="fa-solid fa-user-pen text-primary"></i>
                                                            {{ ucwords(strtolower($topic->user->name)) }}
                                                        </span>
                                                    </h3>
                                                    @if ($topic->description)
                                                        <p
                                                            class="text-base-content/70 mt-1 {{ $topic->is_completed ? 'line-through opacity-50' : '' }}">
                                                            {{ $topic->description }}
                                                        </p>

                                                    @endif

                                                </div>
                                            </div>

                                            @unless ($topic->is_completed)
                                                <button onclick="continueTopic({{ $topic->id }})"
                                                    class="btn btn-ghost btn-sm gap-2 hover:btn-primary">
                                                    Continue
                                                    <i class="fa-solid fa-arrow-right"></i>
                                                </button>
                                            @endunless
                                        </div>

                                        @if ($topic->files->count() > 0)
                                            <div class="divider"></div>
                                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                                @foreach ($topic->files as $file)
                                                    <div class="group relative">
                                                        @if (Str::startsWith($file->type, 'image/'))
                                                            <div class="aspect-square rounded-xl overflow-hidden">
                                                                <img src="{{ Storage::url($file->path) }}"
                                                                    alt="{{ $file->filename }}"
                                                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                                                            </div>
                                                        @else
                                                            <div
                                                                class="aspect-square rounded-xl border-2 border-base-300 flex flex-col items-center justify-center p-4 group-hover:border-primary transition-colors">
                                                                <i
                                                                    class="fa-solid fa-file-lines text-3xl text-primary"></i>
                                                                <span
                                                                    class="mt-2 text-sm text-center line-clamp-2">{{ $file->filename }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-12 text-base-content/60">
                                    <i class="fa-regular fa-clipboard text-4xl mb-4"></i>
                                    <p class="text-lg">No topics yet for this meeting</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endforeach
            <div class="mt-6">
                {{ $meetings->links() }}
            </div>
        </div>
    </div>

    <dialog id="topicModal" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box">
            <h3 class="font-bold text-2xl mb-6">Add New Topic</h3>
            <form method="POST" enctype="multipart/form-data" id="topicForm" class="space-y-6">
                @csrf
                <div class="form-control">
                    <input type="text" name="title" placeholder="Topic Title" class="input input-bordered w-full"
                        required>
                </div>

                <div class="form-control">
                    <textarea name="description" placeholder="Description" class="textarea textarea-bordered w-full h-24"></textarea>
                </div>

                <div class="form-control">
                    <input type="file" name="files[]" multiple class="file-input file-input-bordered w-full"
                        onchange="previewFiles(this)" />
                    <div id="filePreview" class="grid grid-cols-4 gap-2 mt-4"></div>
                </div>

                <div class="modal-action">
                    <button type="button" onclick="closeTopicModal()" class="btn btn-ghost">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Topic</button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>
    <script>
        function generateMeetings() {
            fetch('/meetings/generate', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                }).then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                        // Optional: Show success toast
                        const toast = document.createElement('div');
                        toast.className = 'toast toast-top toast-end';
                        toast.innerHTML = `
                <div class="alert alert-success">
                    <i class="fa-solid fa-check"></i>
                    <span>${data.message}</span>
                </div>
            `;
                        document.body.appendChild(toast);
                        setTimeout(() => toast.remove(), 3000);
                    }
                });
        }

        function openTopicModal(meetingId) {
            const modal = document.getElementById('topicModal');
            const form = document.getElementById('topicForm');
            form.action = `/meetings/${meetingId}/topics`;
            modal.showModal();
        }

        function closeTopicModal() {
            const modal = document.getElementById('topicModal');
            const form = document.getElementById('topicForm');
            form.reset();
            modal.close();
        }

        function previewFiles(input) {
            const preview = document.getElementById('filePreview');
            preview.innerHTML = '';

            Array.from(input.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const div = document.createElement('div');
                    div.className = 'relative group';

                    if (file.type.startsWith('image/')) {
                        div.innerHTML = `
                    <div class="aspect-square rounded-xl overflow-hidden">
                        <img src="${e.target.result}" class="w-full h-full object-cover"/>
                    </div>`;
                    } else {
                        div.innerHTML = `
                    <div class="aspect-square rounded-xl border-2 border-base-300 flex flex-col items-center justify-center p-4">
                        <i class="fa-solid fa-file-lines text-3xl text-primary"></i>
                        <span class="mt-2 text-sm text-center line-clamp-2">${file.name}</span>
                    </div>`;
                    }

                    preview.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        }

        function toggleComplete(topicId) {
            fetch(`/topics/${topicId}/toggle`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                }).then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    }
                });
        }

        function continueTopic(topicId) {
            fetch(`/topics/${topicId}/continue`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                }).then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    }
                });
        }
    </script>
    <div class="toast toast-top toast-end">
        <div id="toast-success" class="alert alert-success hidden">
            <i class="fa-solid fa-check"></i>
            <span>Meetings generated successfully!</span>
        </div>
    </div>
</x-app-layout>
