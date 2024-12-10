<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Report Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-6">
                        <h3 class="text-lg font-medium">{{ $report->date }}</h3>
                        <h1>{{ $report->name }}</h1>
                    </div>

                    <div class="border-t border-gray-200">
                        @forelse($report->tasks as $key => $task)
                        <dl class="divide-y divide-gray-200">
                            <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">{{ $key + 1 }}.  {{ $task->category }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                                    @if($task->batch) Batch: {{ $task->batch }}@endif
                                    @if($task->claim) Claim: {{ $task->claim }}@endif
                                    @if($task->email) Email: {{ $task->email }}@endif
                                </dd>
                            </div>
                        </dl>

                        @empty
                            <div class="py-4 text-center text-gray-500">
                                No tasks found for this report.
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-6">
                        <a href="{{ route('reports.index') }}" class="text-blue-600 hover:text-blue-900">Back to
                            Reports</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

