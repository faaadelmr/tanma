<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-3xl leading-tight">
                {{ __('Daftar User') }}
            </h2>

        </div>
    </x-slot>

    <div class="py-8">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-end mb-4">
                <a href="users/create" class="btn bg-secondary">
                    Tambah Akun
                </a>
            </div>
            <div class="bg-white overflow-hidden shadow-xl rounded-2xl">
                <div class="p-8">
                    <div class="overflow-x-auto">
                        <table class="table w-full">
                            <thead>
                                <tr class="bg-gray-50 border-b">
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600 tracking-wider">Name</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600 tracking-wider">Username & Email</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600 tracking-wider">Role</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600 tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($users as $user)
                                <tr class="hover:bg-gray-50/50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span class="text-gray-900 font-medium">{{ $user->username }}</span>
                                            <span class="text-sm text-gray-500">
                                                {{ $user->email ?: 'Email belum ditambahkan' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                            {{ $user->roles->pluck('name')->join(', ') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex gap-3">
                                            <a href="{{ route('users.edit', $user) }}"
                                               class="btn btn-sm bg-indigo-500 hover:bg-indigo-600 text-white border-0 hover:scale-105 transition-all duration-200">Edit</a>
                                            @if(!$user->roles->pluck('name')->contains('admin'))
                                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-sm bg-rose-500 hover:bg-rose-600 text-white border-0 hover:scale-105 transition-all duration-200"
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
