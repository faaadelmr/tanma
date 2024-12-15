<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Create Task Category
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('task-categories.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium mb-2">Name</label>
                            <input type="text"
                                   id="name"
                                   name="name"
                                   value="{{ old('name') }}"
                                   class="form-input rounded-md shadow-sm mt-1 block w-full @error('name') border-red-500 @enderror"
                                   required
                                   autofocus>
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <h3 class="text-lg font-medium mb-4">Category Properties</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach(['dor_date' => 'DOR Date',
                                        'batch' => 'Batch',
                                        'claim' => 'Claim',
                                        'time_range' => 'Time Range',
                                        'sheets' => 'Sheets',
                                        'email' => 'Email',
                                        'form' => 'Form'] as $key => $label)
                                    <div class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-100">
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="checkbox"
                                                   name="has_{{ $key }}"
                                                   class="form-checkbox h-5 w-5 text-blue-600 rounded"
                                                   {{ old("has_$key") ? 'checked' : '' }}>
                                            <span class="ml-2 text-sm">Has {{ $label }}</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <a href="{{ route('task-categories.index') }}"
                               class="text-gray-600 hover:text-gray-900">
                                Cancel
                            </a>
                            <button type="submit"
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition duration-150 ease-in-out">
                                Create Category
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
