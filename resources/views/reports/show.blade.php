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
                        <h3 class="text-lg font-medium">Report for {{ $report->report_date->format('d/m/Y') }}</h3>
                        <p class="text-gray-600">By: {{ $report->user->name }}</p>
                    </div>

                    <div class="space-y-4">
                        @forelse($report->tasks->groupBy('category_id') as $categoryId => $tasks)
                            <div class="pt-4 border-t">
                                <h4 class="font-medium">{{ $tasks->first()->category->name ?? 'Uncategorized' }}</h4>
                                <ul class="mt-2 space-y-2">
                                    @foreach($tasks as $task)
                                        <li class="flex justify-between items-center py-1 hover:bg-gray-50">
                                            <span class="text-gray-700">{{ $task->description }}</span>
                                            <span class="text-gray-600">{{ number_format($task->quantity) }} {{ $task->unit }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @empty
                            <div class="py-4 text-center text-gray-500">
                                No tasks found for this report.
                            </div>
                        @endforelse                    </div>

                    <div class="mt-6">
                        <a href="{{ route('reports.index') }}" class="text-blue-600 hover:text-blue-900">Back to Reports</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
