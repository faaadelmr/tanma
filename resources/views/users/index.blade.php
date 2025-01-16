<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
            <h2 class="font-bold text-2xl sm:text-3xl leading-tight">
                {{ __('Daftar User') }}
            </h2>
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
                                    <th
                                        class="px-3 sm:px-4 py-2 sm:py-3 text-left text-xs sm:text-sm font-semibold text-gray-600 tracking-wider">
                                        Name</th>
                                    <th
                                        class="hidden sm:table-cell px-3 sm:px-4 py-2 sm:py-3 text-left text-xs sm:text-sm font-semibold text-gray-600 tracking-wider">
                                        Username & Email</th>
                                    <th
                                        class="px-3 sm:px-4 py-2 sm:py-3 text-left text-xs sm:text-sm font-semibold text-gray-600 tracking-wider">
                                        Role</th>
                                    <th
                                        class="px-3 sm:px-4 py-2 sm:py-3 text-left text-xs sm:text-sm font-semibold text-gray-600 tracking-wider">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($users as $user)
                                    @if(!$user->roles->pluck('name')->contains('dev'))
                                        <tr class="hover:bg-gray-50/50 transition-colors duration-150 @if(!$user->is_active) opacity-60  @endif">
                                            <td class="px-3 sm:px-4 py-2 sm:py-3">
                                                <div class="text-sm text-gray-900">{{ $user->name }}</div>
                                                <div class="sm:hidden text-xs text-gray-500">
                                                    {{ $user->username }}
                                                </div>
                                            </td>
                                            <td class="hidden sm:table-cell px-3 sm:px-4 py-2 sm:py-3">
                                                <div class="text-sm text-gray-900">{{ $user->username }}</div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $user->email ?: 'Email belum ditambahkan' }}</div>
                                            </td>
                                            <td class="px-3 sm:px-4 py-2 sm:py-3">
                                                <span class="px-2 py-1 text-xs rounded-full bg-indigo-100 text-indigo-800">
                                                    {{ $user->roles->pluck('name')->join(', ') }} @if(!$user->is_active) <span class="">:Tidak aktif</span> @endif
                                                </span>
                                            </td>
                                            <td class="px-3 sm:px-4 py-2 sm:py-3">
                                                <div class="flex gap-2">
                                                    <a href="{{ route('users.edit', $user) }}"
                                                        class="btn btn-xs bg-indigo-500 hover:bg-indigo-600 text-white border-0">Edit</a>
                                                    @if(!$user->is_active)
                                                        <form action="{{ route('users.destroy', $user) }}" method="POST"
                                                            class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button"
                                                                class="btn btn-xs bg-blue-500 hover:bg-blue-600 hover:opacity-1000 text-white border-0"
                                                                onclick="confirmDeactivate(this.form)"
                                                                @if($user->is_active) disabled @endif>Aktifkan User</button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDeactivate(form) {
            Swal.fire({
                title: 'Apakah kamu yakin?',
                text: "User akan diaktifkan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, aktifkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    </script>
</x-app-layout>