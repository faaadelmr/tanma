<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Report Harian') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between mb-6 px-4">
                @role('admin')
                    <h2 class="btn btn-accent"><a href="{{ route('task-categories.index') }}">Tambah Kategori Tugas</a></h2>
                @endrole
                <a href="{{ route('daily-reports.create') }}" class="btn btn-primary">
                    Buat Report Baru
                </a>
            </div>

            <div class="grid gap-6">
                @php
                    $groupedReports = $reports->groupBy(function ($report) {
                        return $report->report_date->format('d/m/Y');
                    });
                @endphp

                @foreach ($groupedReports as $date => $dateReports)
                    <div class="card bg-base-100 shadow-xl">
                        <div class="card-body">
                            <h3 class="card-title text-xl mb-4">{{ $date }}</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach ($dateReports as $report)
                                    <div class="card bg-base-100 shadow-xl">
                                        <div class="card-body">
                                            <div class="flex items-center justify-between mb-3">
                                                <div class="flex items-center">
                                                    <div class="badge badge-primary">Report</div>
                                                    <span class="ml-2 font-medium">{{ $report->user->name }}</span>
                                                </div>
                                            </div>
                                            <div class="space-y-2">
                                                @foreach ($report->tasks as $index => $task)
                                                    <div class="flex gap-2">
                                                        <span class="font-medium">{{ $index + 1 }}.</span>
                                                        <div>
                                                            <span class="font-medium">{{ $task->category->name }}</span>
                                                            @if ($task->category->id && $task->task_date)
                                                                <span class="text-bold">DOR
                                                                    {{ $task->task_date }}</span>
                                                            @endif
                                                            <span class="text-gray-600">:
                                                                @if ($task->batch_count)
                                                                    {{ $task->batch_count }} Batch,
                                                                @endif
                                                                @if ($task->claim_count)
                                                                    {{ $task->claim_count }} Klaim
                                                                @endif
                                                                @if ($task->start_time && $task->end_time)
                                                                    {{ \Carbon\Carbon::parse($task->start_time)->format('H:i') }}
                                                                    -
                                                                    {{ \Carbon\Carbon::parse($task->end_time)->format('H:i') }}
                                                                @endif
                                                                @if ($task->sheet_count)
                                                                    {{ $task->sheet_count }} Lembar
                                                                @endif
                                                                @if ($task->email)
                                                                    {{ $task->email }} Email
                                                                @endif
                                                                @if ($task->form)
                                                                    {{ $task->form }} Form
                                                                @endif
                                                            </span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            @role('admin')
                                                <button
                                                    onclick="document.getElementById('delete-modal-{{ $report->id }}').showModal()"
                                                    class="ml-4 mb-2 btn btn-error btn-sm">
                                                    Hapus
                                                </button>
                                                <dialog id="delete-modal-{{ $report->id }}" class="modal">
                                                    <div class="modal-box">
                                                        <h3 class="font-bold text-lg">Konfirmasi Hapus</h3>
                                                        <p class="py-4">Apakah anda yakin ingin menghapus report ini?</p>
                                                        <div class="modal-action">
                                                            <form action="{{ route('daily-reports.destroy', $report) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-error">Ya,
                                                                    Hapus</button>
                                                            </form>
                                                            <form method="dialog">
                                                                <button class="btn">Batal</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </dialog>
                                            @endrole

                                            <div class="mr-2 mb-1 text-sm text-gray-500 flex items-center gap-1">
                                                <i class="fa-regular fa-clock"></i>
                                                {{ $report->created_at->locale('id')->diffForHumans() }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $reports->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
