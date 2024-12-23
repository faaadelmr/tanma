<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-primary font-bold text-2xl">{{ __('Meeting Mingguan') }}</h2>
            <button onclick="generateMeetings()" class="btn btn-primary btn-sm gap-2">
                <i class="fa-solid fa-calendar-plus"></i>
                Buat Meeting
            </button>
        </div>
    </x-slot>
    @if(session('success'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    timer: 3000,
                    showConfirmButton: false
                });
            </script>
        @endif
        @if(session('error'))
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: "{{ session('error') }}",
                    timer: 3000,
                    showConfirmButton: false
                });
            </script>
        @endif

    <div class="py-6 bg-base-200/50 min-h-screen">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid gap-4 sm:gap-6">
                @forelse ($meetings as $meeting)
                    <div class="card bg-base-100 shadow-md hover:shadow-lg transition-all duration-300">
                        <div class="card-body p-4 sm:p-6">
                            <div class="flex flex-wrap justify-between items-center gap-4 border-b border-base-200 pb-4">
                                <div class="flex items-center gap-3">
                                    <div class="badge badge-primary p-3">
                                        <i class="fa-solid fa-calendar-week text-lg"></i>
                                    </div>
                                    <div>
                                        <h2 class="text-lg sm:text-xl font-bold">
                                            {{ $meeting->meeting_date->isoFormat('dddd', 'id') }}
                                        </h2>
                                        <p class="text-sm text-base-content/70">
                                            {{ $meeting->meeting_date->isoFormat('D MMMM Y') }}
                                        </p>
                                    </div>
                                </div>
                                <button 
                                    onclick="openTopicModal({{ $meeting->id }})" 
                                    class="btn btn-primary btn-sm">
                                    <i class="fa-solid fa-plus"></i>
                                    <span class="hidden sm:inline">Tambah Pembahasan</span>
                                </button>
                            </div>

                            <div class="mt-4 space-y-3">
                                @forelse($meeting->topics as $topic)
                                    <div class="group relative bg-base-100 rounded-lg border-2 p-4
                                        {{ $topic->is_completed ? 'border-success/30 bg-success/5' : 'border-base-200' }}
                                        hover:border-primary/30 transition-colors">
                                        <div class="flex items-start gap-3">
                                            <button 
                                                onclick="toggleComplete({{ $topic->id }})"
                                                class="btn btn-circle btn-xs {{ $topic->is_completed ? 'btn-success' : 'btn-warning' }} mt-1">
                                                <i class="fa-solid {{ $topic->is_completed ? 'fa-check' : 'fa-clock' }}"></i>
                                            </button>
                                            
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center justify-between gap-2">
                                                    <h3 class="font-medium {{ $topic->is_completed | $topic->is_continued ? 'line-through opacity-50' : '' }}">
                                                        {{ $topic->title }}
                                                        <span class="inline-flex gap-2 ml-2">
 
                                                            @if($topic->continued_from_id)
                                                                <span class="badge badge-info badge-sm">
                                                                    <i class="fa-solid fa-arrow-left mr-1"></i>
                                                                    Dari meeting sebelumnya
                                                                </span>
                                                            @endif
                                                            
                                                            @if($topic->is_completed && $topic->is_completed = 2)
                                                            <span class="badge badge-success badge-sm">
                                                                <i class="fa-solid fa-check mr-1"></i>
                                                                Selesai
                                                            </span>
                                                        @elseif($topic->is_completed == 0 && $topic->continued_from_id == false && $topic->is_continued == false)
                                                            <span class="badge badge-warning badge-sm">
                                                                <i class="fa-solid fa-magnifying-glass mr-1"></i>
                                                                Pembahasan baru!
                                                            </span>
                                                        @endif      
                                                        @if($topic->is_continued)
                                                        <span class="badge badge-warning badge-sm">
                                                            <i class="fa-solid fa-arrow-right mr-1"></i>
                                                            Dilanjutkan ke meeting berikutnya
                                                        </span>
                                                        @endif                             
                                                        </span>
                                                    </h3>
                                                    @unless($topic->is_completed || $topic->is_continued)
                                                        <button 
                                                            onclick="continueTopic({{ $topic->id }})"
                                                            class="btn hover:btn-info btn-xs opacity-0 group-hover:opacity-100 transition-opacity">
                                                            Teruskan
                                                            <i class="fa-solid fa-arrow-right"></i>
                                                        </button>
                                                    @endunless
                                                </div>
                                                
                                                @if($topic->description)
                                                    <p class="text-sm text-base-content/70 mt-1 {{ $topic->is_completed ? 'line-through opacity-50' : '' }}">
                                                        {{ $topic->description }}
                                                    </p>
                                                @endif

                                                <div class="flex items-center gap-2 mt-2">
                                                    <span class="text-xs text-base-content/50">
                                                        <i class="fa-solid fa-user-pen text-primary/70"></i>
                                                        {{ ucwords(strtolower($topic->user->name)) }}
                                                    </span>
                                                    <span class="text-xs text-base-content/50">
                                                        <i class="fa-regular fa-clock text-primary/70"></i>
                                                        {{ $topic->created_at->locale('id')->diffForHumans() }}
                                                    </span>

                                                </div>
                                            </div>
                                        </div>

                                        @if($topic->files->count() > 0)
                                            <div class="mt-3 pt-3 border-t border-base-200">
                                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                                    @foreach($topic->files as $file)
                                                        <div class="aspect-square rounded-lg overflow-hidden bg-base-200/50">
                                                            @if(Str::startsWith($file->type, 'image/'))
                                                                <img 
                                                                    src="{{ Storage::url($file->path) }}"
                                                                    alt="{{ $file->filename }}"
                                                                    class="w-full h-full object-cover hover:scale-105 transition-transform"
                                                                />
                                                            @else
                                                                <div class="w-full h-full flex flex-col items-center justify-center p-2">
                                                                    <i class="fa-solid fa-file-lines text-xl text-primary/70"></i>
                                                                    <span class="text-xs text-center mt-1 line-clamp-2">
                                                                        {{ $file->filename }}
                                                                    </span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @empty
                                    <div class="text-center py-8 text-base-content/50">
                                        <i class="fa-regular fa-clipboard text-3xl mb-2"></i>
                                        <p>No topics yet</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="card bg-base-100 shadow-md p-8">
                        <div class="text-center text-base-content/50">
                            <i class="fa-regular fa-calendar-xmark text-4xl mb-3"></i>
                            <p class="text-lg">No meetings scheduled</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $meetings->links() }}
            </div>
        </div>
    </div>

    <dialog id="topicModal" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box max-w-2xl bg-base-100">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-primary">New Discussion Topic</h3>
                <button onclick="closeTopicModal()" class="btn btn-ghost btn-sm btn-circle">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form method="POST" enctype="multipart/form-data" id="topicForm" class="space-y-6">
                @csrf
                
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">Topic Title</span>
                        <span class="label-text-alt text-error">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="title" 
                        class="input input-bordered w-full" 
                        placeholder="Enter topic title"
                        required
                    >
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">Description</span>
                    </label>
                    <textarea 
                        name="description" 
                        class="textarea textarea-bordered min-h-[120px]" 
                        placeholder="Provide additional details about the topic"
                    ></textarea>
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">Attachments</span>
                        <span class="label-text-alt text-base-content/60">Optional</span>
                    </label>
                    
                    <div class="flex flex-col gap-4">
                        <input 
                            type="file" 
                            name="files[]" 
                            multiple 
                            class="file-input file-input-bordered w-full"
                            onchange="previewFiles(this)" 
                        />
                        
                        <div id="filePreview" class="grid grid-cols-2 sm:grid-cols-4 gap-3"></div>
                    </div>
                </div>

                <div class="modal-action flex justify-end gap-2 pt-4 border-t border-base-200">
                    <button 
                        type="button" 
                        onclick="closeTopicModal()" 
                        class="btn btn-ghost"
                    >
                        Cancel
                    </button>
                    <button 
                        type="submit" 
                        class="btn btn-primary"
                    >
                        <i class="fa-solid fa-plus"></i>
                        Create Topic
                    </button>
                </div>
            </form>
        </div>

        <form method="dialog" class="modal-backdrop bg-base-200/80">
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
            })
            .then(response => response.json())
            // .then(data => {
            //     if (data.success) {
            //         window.location.reload();
            //         const toast = document.createElement('div');
            //         toast.className = 'toast toast-top toast-end';
            //         toast.innerHTML = `
            //             <div class="alert alert-success">
            //                 <i class="fa-solid fa-check"></i>
            //                 <span>${data.message}</span>
            //             </div>
            //         `;
            //         document.body.appendChild(toast);
            //         setTimeout(() => toast.remove(), 3000);
            //     }
            // })
            ;
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
            const preview = document.getElementById('filePreview');
            
            form.reset();
            preview.innerHTML = '';
            modal.close();
        }

        function previewFiles(input) {
            const preview = document.getElementById('filePreview');
            preview.innerHTML = '';

            Array.from(input.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const div = document.createElement('div');
                    div.className = 'relative group aspect-square rounded-lg overflow-hidden border-2 border-base-200';

                    if (file.type.startsWith('image/')) {
                        div.innerHTML = `
                            <img 
                                src="${e.target.result}" 
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform"
                                alt="${file.name}"
                            />
                            <div class="absolute inset-0 bg-base-100/80 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <span class="text-xs text-center px-2 line-clamp-2">${file.name}</span>
                            </div>
                        `;
                    } else {
                        div.innerHTML = `
                            <div class="w-full h-full flex flex-col items-center justify-center p-3 group-hover:bg-base-200/50 transition-colors">
                                <i class="fa-solid fa-file-lines text-2xl text-primary mb-2"></i>
                                <span class="text-xs text-center line-clamp-2">${file.name}</span>
                            </div>
                        `;
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
            })
            .then(response => response.json())
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
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                }
            });
        }
    </script>
</x-app-layout>
