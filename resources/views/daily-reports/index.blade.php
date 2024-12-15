<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between mb-6 px-4">
                <h2 class="text-2xl font-bold">Daily Reports</h2>
                <a href="{{ route('daily-reports.create') }}" class="btn btn-primary">
                    Create New Report
                </a>
            </div>

            <div class="grid gap-6">
                @php
                    $groupedReports = $reports->groupBy(function($report) {
                        return $report->report_date->format('d/m/Y');
                    });
                @endphp

                @foreach($groupedReports as $date => $dateReports)
                    <div class="card bg-base-100 shadow-xl">
                        <div class="card-body">
                            <h3 class="card-title text-xl mb-4">{{ $date }}</h3>

                            @foreach($dateReports as $report)
                                <div class="mb-6">
                                    <div class="flex items-center mb-3">
                                        <div class="badge badge-primary">Report</div>
                                        <span class="ml-2 font-medium">{{ $report->user->name }}</span>
                                    </div>

                                    <div class="space-y-2 pl-4">
                                        @foreach($report->tasks as $index => $task)
                                            <div class="flex gap-2">
                                                <span class="font-medium">{{ $index + 1 }}.</span>
                                                <div>
                                                    <span class="font-medium">{{ $task->category->name }}</span>
                                                    @if($task->category->id)
                                                        <span class="text-sm text-gray-600">DOR {{ $task->task_date }}</span>
                                                    @endif
                                                    <span class="text-gray-600">:
                                                        @if($task->batch_count)
                                                            {{ $task->batch_count }} Batch
                                                        @endif
                                                        @if($task->claim_count)
                                                            {{ $task->claim_count }} Klaim
                                                        @endif
                                                        @if($task->start_time && $task->end_time)
                                                            {{ $task->start_time }} - {{ $task->end_time }}
                                                        @endif
                                                        @if($task->sheet_count)
                                                            {{ $task->sheet_count }} Lembar
                                                        @endif
                                                        @if($task->email)
                                                            {{ $task->email }} Email
                                                        @endif
                                                        @if($task->form)
                                                            {{ $task->form }} Form
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
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
