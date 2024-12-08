<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Daftar Pengguna') }}
        </h2>
    </x-slot>

    <div class="py-8 shadow-xl">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden shadow-sm sm:rounded-lg">
                <div class="">
                    <div class="flex justify-between mb-6">
                        <h2 class="text-2xl font-bold"></h2>
                        <a href="users/create" class="btn btn-secondary">Tambah Akun</a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="table w-full">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Username & Email</th>
                                    <th>Role</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                <tr class="hover">
                                    <td>{{ $user->name }}</td>
                                    <td>
                                        <div class="flex flex-col">
                                            <span>{{ $user->username }}</span>
                                            @if($user->email)
                                                <span>{{ $user->email }}</span>
                                            @else
                                                <span>Email belum ditambahkan</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>{{ $user->roles->pluck('name')->join(', ') }}</td>
                                    <td class="flex gap-2">
                                        <a href="{{ route('users.edit', $user) }}" class="btn btn-sm">Edit</a>
                                        @if(!$user->roles->pluck('name')->contains('admin'))
                                        <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-error" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                        @endif
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
