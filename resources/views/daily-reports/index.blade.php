<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between mb-6">
                        <h2 class="text-2xl font-bold">Daily Reports</h2>
                        <a href="{{ route('daily-reports.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Create New Report
                        </a>
                    </div>

                    @foreach($reports as $report)
                        <div class="mb-8 p-6 border rounded-lg">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-xl font-semibold">{{ $report->report_date->format('d/m/Y') }}</h3>
                                <p class="text-gray-600">{{ $report->user->name }}</p>
                            </div>

                            <div class="space-y-3">
                                @foreach($report->tasks as $task)
                                    <div class="pl-4 border-l-4 border-gray-200">
                                        <p class="font-medium">{{ $task->category->name }}
                                            @if($task->task_date)
                                                DOR {{ $task->task_date }}
                                            @endif
                                        </p>
                                        <p class="text-gray-600">
                                            @if($task->batch_count)
                                                {{ $task->batch_count }} Batch,
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
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    {{ $reports->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
