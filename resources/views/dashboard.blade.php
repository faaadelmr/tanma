<x-app-layout>
    <div class="p-4 space-y-4 sm:p-6 sm:space-y-6">
        <!-- Header with Quick Stats -->
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-2 lg:grid-cols-4 sm:gap-4">
            @foreach($performanceMetrics as $metric => $value)
            <div class="rounded-lg shadow-md transition-shadow stat bg-base-100 hover:shadow-lg">
                <div class="text-xs stat-title sm:text-sm">{{ Str::title(str_replace('_', ' ', $metric)) }}</div>
                <div class="text-sm stat-value sm:text-lg md:text-xl">
                    @if($metric === 'top_performers')
                        <div class="overflow-x-auto">
                            <table class="table w-full table-compact sm:table-normal">
                                @foreach($value as $performer)
                                    <tr>
                                        <td class="text-xs sm:text-sm">{{ $performer['name'] }}</td>
                                        <td class="text-xs sm:text-sm">{{ $performer['completion_rate'] }}%</td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    @else
                        {{ is_numeric($value) ? number_format($value, 0, ',', '.') : $value }}
                    @endif
                </div>
                <div class="text-xs stat-desc">{{ now()->format('F Y') }}</div>
            </div>
            @endforeach
        </div>

        <!-- Category Groups Tabs -->
        <div class="overflow-x-auto whitespace-nowrap tabs tabs-boxed">
            @foreach($groupedCategories as $groupName => $categories)
            <a class="tab tab-sm sm:tab-md {{ $loop->first ? 'tab-active' : '' }}"
               onclick="switchCategoryGroup('{{ $groupName }}')">
                {{ $groupName }}
            </a>
            @endforeach
        </div>

        <!-- Dynamic Content Area -->
        @foreach($groupedCategories as $groupName => $categories)
        <div id="group-{{ $groupName }}" class="category-group {{ !$loop->first ? 'hidden' : '' }}">
            <!-- Metrics Cards -->
            <div class="grid grid-cols-1 gap-3 mb-4 sm:grid-cols-2 lg:grid-cols-3 sm:gap-4 sm:mb-6">
                @foreach($categories as $category)
                <div class="shadow-md transition-shadow card bg-base-100 hover:shadow-lg">
                    <div class="p-3 card-body sm:p-4">
                        <h3 class="flex justify-between items-center text-sm card-title sm:text-base">
                            {{ $category->name }}
                            <span class="text-xs badge badge-primary">{{ $category->tasks_count ?? 0 }} Tasks</span>
                        </h3>
                        <!-- Rest of the card content with responsive classes -->
                    </div>
                </div>
                @endforeach
            </div>

            
            <!-- Chart with responsive height -->
            <div class="shadow-md card bg-base-100">
                <div class="p-3 card-body sm:p-4">
                    <h2 class="text-sm card-title sm:text-base">{{ $groupName }} Tasks Overview</h2>
                    <div class="h-60 sm:h-80">
                        <canvas id="taskChart-{{ $groupName }}"></canvas>
                    </div>
                </div>
            </div>
        </div>
        @endforeach

        <!-- Top Performers Table -->
        <div class="shadow-md card bg-base-100">
            <div class="p-3 card-body sm:p-4">
                <h2 class="mb-2 text-sm card-title sm:text-base">Top Performers</h2>
                <div class="overflow-x-auto">
                    <table class="table w-full table-compact sm:table-normal">
                        <thead>
                            <tr>
                                <th class="text-xs sm:text-sm">User</th>
                                <th class="text-xs sm:text-sm">Total Tasks</th>
                                <th class="text-xs sm:text-sm">Completion Rate</th>
                                <th class="text-xs sm:text-sm">Most Productive Category</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($performanceMetrics['top_performers'] as $performer)
                            <tr>
                                <td class="text-xs sm:text-sm">{{ $performer['name'] }}</td>
                                <td class="text-xs sm:text-sm">{{ $performer['total_tasks'] }}</td>
                                <td class="text-xs sm:text-sm">{{ $performer['completion_rate'] }}%</td>
                                <td class="text-xs sm:text-sm">{{ $performer['most_productive_category'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


    <script>
            function switchCategoryGroup(groupName) {
        // Hide all category groups
        document.querySelectorAll('.category-group').forEach(el => el.classList.add('hidden'));

        // Show selected group
        document.getElementById(`group-${groupName}`).classList.remove('hidden');

        // Update active tab state
        document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('tab-active'));
        event.target.classList.add('tab-active');

        // Refresh charts for the selected group
        createTaskCharts();
    }

        function createTaskCharts() {
            @foreach($groupedCategories as $groupName => $categories)
                new Chart(document.getElementById('taskChart-{{ $groupName }}'), {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($categories->pluck('name')) !!},
                        datasets: [{
                            label: 'Batch Count',
                            data: {!! json_encode($categories->pluck('metrics.batch_count')) !!},
                            backgroundColor: '#9333EA'
                        }, {
                            label: 'Claim Count',
                            data: {!! json_encode($categories->pluck('metrics.claim_count')) !!},
                            backgroundColor: '#3B82F6'
                        }, {
                            label: 'Sheet Count',
                            data: {!! json_encode($categories->pluck('metrics.sheet_count')) !!},
                            backgroundColor: '#22C55E'
                        }, {
                            label: 'Email',
                            data: {!! json_encode($categories->pluck('metrics.email')) !!},
                            backgroundColor: '#EAB308'
                        }, {
                            label: 'Form',
                            data: {!! json_encode($categories->pluck('metrics.form')) !!},
                            backgroundColor: '#EC4899'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            @endforeach
        }

        document.addEventListener('DOMContentLoaded', createTaskCharts);
    </script>
</x-app-layout>
