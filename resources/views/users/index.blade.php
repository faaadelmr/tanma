<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
            <h2 class="font-bold text-2xl sm:text-3xl leading-tight">
                {{ __('Daftar User') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-4 sm:py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-end mb-4">
                <a href="users/create" class="btn btn-sm sm:btn-md bg-secondary w-full sm:w-auto">
                    Tambah Akun
                </a>
            </div>
            <div class="bg-white overflow-hidden shadow-xl rounded-lg sm:rounded-2xl">
                <div class="p-3 sm:p-6">
                    <div class="overflow-x-auto -mx-4 sm:mx-0">
                        <table class="table w-full">
                            <thead>
                                <tr class="bg-gray-50 border-b">
                                    <th class="px-3 sm:px-4 py-2 sm:py-3 text-left text-xs sm:text-sm font-semibold text-gray-600 tracking-wider">Name</th>
                                    <th class="hidden sm:table-cell px-3 sm:px-4 py-2 sm:py-3 text-left text-xs sm:text-sm font-semibold text-gray-600 tracking-wider">Username & Email</th>
                                    <th class="px-3 sm:px-4 py-2 sm:py-3 text-left text-xs sm:text-sm font-semibold text-gray-600 tracking-wider">Role</th>
                                    <th class="px-3 sm:px-4 py-2 sm:py-3 text-left text-xs sm:text-sm font-semibold text-gray-600 tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($users as $user)
                                <tr class="hover:bg-gray-50/50 transition-colors duration-150">
                                    <td class="px-3 sm:px-4 py-2 sm:py-3">
                                        <div class="text-sm text-gray-900">{{ $user->name }}</div>
                                        <div class="sm:hidden text-xs text-gray-500">
                                            {{ $user->username }}
                                        </div>
                                    </td>
                                    <td class="hidden sm:table-cell px-3 sm:px-4 py-2 sm:py-3">
                                        <div class="text-sm text-gray-900">{{ $user->username }}</div>
                                        <div class="text-xs text-gray-500">{{ $user->email ?: 'Email belum ditambahkan' }}</div>
                                    </td>
                                    <td class="px-3 sm:px-4 py-2 sm:py-3">
                                        <span class="px-2 py-1 text-xs rounded-full bg-indigo-100 text-indigo-800">
                                            {{ $user->roles->pluck('name')->join(', ') }}
                                        </span>
                                    </td>
                                    <td class="px-3 sm:px-4 py-2 sm:py-3">
                                        <div class="flex gap-2">
                                            <a href="{{ route('users.edit', $user) }}"
                                               class="btn btn-xs bg-indigo-500 hover:bg-indigo-600 text-white border-0">Edit</a>
                                            @if(!$user->roles->pluck('name')->contains('admin'))
                                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-xs bg-rose-500 hover:bg-rose-600 text-white border-0"
                                                        onclick="return confirm('Are you sure?')">Delete</button>
                                            </form>
                                            @endif
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
    </div>
</x-app-layout>
