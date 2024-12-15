<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-primary-content leading-tight">
            Task Categories
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden border-2 shadow-sm sm:rounded-lg">
                <div class="p-6  ">
                    <div class="flex justify-end mb-4">
                        <a href="{{ route('task-categories.create') }}" class="bg-primary-content text-white font-bold py-2 px-4 rounded">
                            Add Category
                        </a>
                    </div>

                    <table class="min-w-full">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 border-b">Nama Tugas</th>
                                <th class="px-6 py-3 border-b">Fungsi</th>
                                <th class="px-6 py-3 border-b">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $category)
                            <tr>
                                <td class="px-6 py-4 border-b">{{ $category->name }}</td>
                                <td class="px-6 py-4 border-b ">
                                    @if($category->has_dor_date) <span class="badge text-primary-content  bg-primary">DOR</span> @endif
                                    @if($category->has_batch) <span class="badge text-primary-content t bg-primary">Batch</span> @endif
                                    @if($category->has_claim) <span class="badge text-primary-content bg-primary">Claim</span> @endif
                                    @if($category->has_time_range) <span class="badge text-primary-content bg-primary">Waktu</span> @endif
                                    @if($category->has_sheets) <span class="badge text-primary-content bg-primary">Lembar</span> @endif
                                    @if($category->has_email) <span class="badge text-primary-content bg-primary">Email</span> @endif
                                    @if($category->has_form) <span class="badge text-primary-content bg-primary">Form</span> @endif
                                </td>
                                <td class="px-6 py-4 border-b">
                                    <a href="{{ route('task-categories.edit', $category->id) }}" class="btn btn-secondary">Edit</a>
                                    <label for="delete-modal-{{$category->id}}" class="btn btn-error">Delete</label>
                                    <!-- Delete Confirmation Modal -->
                                    <input type="checkbox" id="delete-modal-{{$category->id}}" class="modal-toggle" />
                                    <div class="modal">
                                        <div class="modal-box bg-white text-black">
                                            <h3 class="font-bold text-lg">Konfirmasi Hapus</h3>
                                            <p class="py-4">Apakah kamu yakin ingin menghapus "{{$category->name}}"?</p>
                                            <div class="modal-action text-accent-content">
                                                <form action="{{ route('task-categories.destroy', $category->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-error">Ya, Hapus</button>
                                                </form>
                                                <label for="delete-modal-{{$category->id}}" class="btn">Batal</label>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
