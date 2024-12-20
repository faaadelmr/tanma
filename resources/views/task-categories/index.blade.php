<x-app-layout>
    <x-slot name="header">
        <h2 class="text-primary font-semibold text-xl md:text-2xl leading-tight">
            {{ __('Tugas Kategori') }}
        </h2>
    </x-slot>

    <div class="py-6 md:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-end mb-4">
                <a href="{{ route('task-categories.create') }}" class="btn btn-secondary text-sm md:text-base w-full md:w-auto">
                    Tambah Kategori Tugas
                </a>
            </div>
            <div class="bg-white overflow-x-auto border-2 shadow-sm rounded-lg">
                <div class="p-3 md:p-6">
                    <table class="min-w-full text-black text-sm md:text-base">
                        <thead>
                            <tr>
                                <th class="px-3 md:px-6 py-2 md:py-3 border-b">Nama Tugas</th>
                                <th class="px-3 md:px-6 py-2 md:py-3 border-b">Fungsi</th>
                                <th class="px-3 md:px-6 py-2 md:py-3 border-b">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $category)
                            <tr>
                                <td class="px-3 md:px-6 py-2 md:py-4 border-b">{{ $category->name }}</td>
                                <td class="px-3 md:px-6 py-2 md:py-4 border-b">
                                    <div class="flex flex-wrap gap-1">
                                        @if($category->has_dor_date) <span class="badge badge-sm md:badge-md text-primary-content bg-primary">DOR</span> @endif
                                        @if($category->has_batch) <span class="badge badge-sm md:badge-md text-primary-content bg-primary">Batch</span> @endif
                                        @if($category->has_claim) <span class="badge badge-sm md:badge-md text-primary-content bg-primary">Claim</span> @endif
                                        @if($category->has_time_range) <span class="badge badge-sm md:badge-md text-primary-content bg-primary">Waktu</span> @endif
                                        @if($category->has_sheets) <span class="badge badge-sm md:badge-md text-primary-content bg-primary">Lembar</span> @endif
                                        @if($category->has_email) <span class="badge badge-sm md:badge-md text-primary-content bg-primary">Email</span> @endif
                                        @if($category->has_form) <span class="badge badge-sm md:badge-md text-primary-content bg-primary">Form</span> @endif
                                    </div>
                                </td>
                                <td class="px-3 md:px-6 py-2 md:py-4 border-b">
                                    <div class="flex flex-col md:flex-row gap-2">
                                        <a href="{{ route('task-categories.edit', $category->id) }}" class="btn btn-secondary btn-sm md:btn-md">Edit</a>
                                        <label for="delete-modal-{{$category->id}}" class="btn btn-error btn-sm md:btn-md">Delete</label>
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
