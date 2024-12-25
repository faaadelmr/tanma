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
                                        <td class="text-xs sm:text-sm">{{ $performer['average_score'] }}</td>
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

        <!-- Time Period Selector -->
        <div class="flex gap-2 mb-4">
            <select id="timePeriod" class="select select-bordered w-full max-w-xs" onchange="updateCharts(this.value)">
                <option value="week">Mingguan</option>
                <option value="month">Bulanan</option>
                <option value="year">Tahunan</option>
            </select>
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
            <div class="shadow-md card bg-base-100">
                <div class="p-3 card-body sm:p-4">
                    <h2 class="text-sm card-title sm:text-base">{{ $groupName }} Data Chart</h2>
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
                                <th class="text-xs sm:text-sm">Nama</th>
                                <th class="text-xs sm:text-sm">Skor Rata-rata</th>
                                <th class="text-xs sm:text-sm">Penyelesaian Rata-rata</th>
                                <th class="text-xs sm:text-sm">Tugas Paling Produktif</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($performanceMetrics['top_performers'] as $performer)
                            <tr>
                                <td class="text-xs sm:text-sm">{{ $performer['name'] }}</td>
                                <td class="text-xs sm:text-sm">{{ $performer['average_score'] }}</td>
                                <td class="text-xs sm:text-sm">{{ $performer['completion_rate'] }}%</</td>
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
        let charts = {};

        function createChart(groupName, data) {
            const ctx = document.getElementById(`taskChart-${groupName}`).getContext('2d');
    
            if (charts[groupName]) {
                charts[groupName].destroy();
            }

            const categories = Object.keys(data);
            const chartData = [];

            categories.forEach(category => {
                if (category.startsWith(groupName)) {
                    chartData.push(...data[category]);
                }
            });

            const labels = [...new Set(chartData.map(item => item.date))].sort();

            // Calculate totals
            const totals = {
                batch: labels.reduce((sum, date) => {
                    const item = chartData.find(d => d.date === date);
                    return sum + (item ? item.batch_count : 0);
                }, 0),
                claim: labels.reduce((sum, date) => {
                    const item = chartData.find(d => d.date === date);
                    return sum + (item ? item.claim_count : 0);
                }, 0),
                sheet: labels.reduce((sum, date) => {
                    const item = chartData.find(d => d.date === date);
                    return sum + (item ? item.sheet_count : 0);
                }, 0),
                email: labels.reduce((sum, date) => {
                    const item = chartData.find(d => d.date === date);
                    return sum + (item ? item.email : 0);
                }, 0),
                form: labels.reduce((sum, date) => {
                    const item = chartData.find(d => d.date === date);
                    return sum + (item ? item.form : 0);
                }, 0)
            };

            const datasets = [
                {
                    label: `Batch (Total: ${totals.batch})`,
                    borderColor: '#9333EA',
                    backgroundColor: '#9333EA20',
                    data: labels.map(date => {
                        const item = chartData.find(d => d.date === date);
                        return item ? item.batch_count : 0;
                    }),
                    tension: 0.4,
                    fill: true
                },
                {
                    label: `Claim (Total: ${totals.claim})`,
                    borderColor: '#3B82F6',
                    backgroundColor: '#3B82F620',
                    data: labels.map(date => {
                        const item = chartData.find(d => d.date === date);
                        return item ? item.claim_count : 0;
                    }),
                    tension: 0.4,
                    fill: true
                },
                {
                    label: `Sheet (Total: ${totals.sheet})`,
                    borderColor: '#22C55E',
                    backgroundColor: '#22C55E20',
                    data: labels.map(date => {
                        const item = chartData.find(d => d.date === date);
                        return item ? item.sheet_count : 0;
                    }),
                    tension: 0.4,
                    fill: true
                },
                {
                    label: `Email (Total: ${totals.email})`,
                    borderColor: '#EAB308',
                    backgroundColor: '#EAB30820',
                    data: labels.map(date => {
                        const item = chartData.find(d => d.date === date);
                        return item ? item.email : 0;
                    }),
                    tension: 0.4,
                    fill: true
                },
                {
                    label: `Form (Total: ${totals.form})`,
                    borderColor: '#EC4899',
                    backgroundColor: '#EC489920',
                    data: labels.map(date => {
                        const item = chartData.find(d => d.date === date);
                        return item ? item.form : 0;
                    }),
                    tension: 0.4,
                    fill: true
                }
            ];

            charts[groupName] = new Chart(ctx, {
                type: 'line',
                data: { labels, datasets },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                drawBorder: false,
                                color: '#E5E7EB40'
                            },
                            ticks: {
                                precision: 0
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 20
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(255, 255, 255, 0.9)',
                            titleColor: '#1F2937',
                            bodyColor: '#1F2937',
                            borderColor: '#E5E7EB',
                            borderWidth: 1,
                            padding: 12,
                            displayColors: true,
                            callbacks: {
                                label: function(context) {
                                    return ` ${context.dataset.label}: ${context.parsed.y}`;
                                }
                            }
                        }
                    }
                }
            });
        }
        function switchCategoryGroup(groupName) {
            document.querySelectorAll('.category-group').forEach(el => el.classList.add('hidden'));
            document.getElementById(`group-${groupName}`).classList.remove('hidden');
            document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('tab-active'));
            event.target.classList.add('tab-active');
        }
        function updateCharts(period) {
            // Tampilkan loading state
            document.querySelectorAll('canvas').forEach(canvas => {
                canvas.style.opacity = '0.5';
            });

            fetch(`/chart-data/${period}`)
                .then(response => response.json())
                .then(data => {
                    // Reset opacity
                    document.querySelectorAll('canvas').forEach(canvas => {
                        canvas.style.opacity = '1';
                    });
                    
                    if (Object.keys(data).length > 0) {
                        Object.entries(data).forEach(([groupName, chartData]) => {
                            if (document.getElementById(`taskChart-${groupName}`)) {
                                createChart(groupName, { [groupName]: chartData });
                            }
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Reset opacity jika terjadi error
                    document.querySelectorAll('canvas').forEach(canvas => {
                        canvas.style.opacity = '1';
                    });
                });
        }

        document.addEventListener('DOMContentLoaded', () => {
            const initialPeriod = document.getElementById('timePeriod').value;
            updateCharts(initialPeriod);
        });
    </script>
</x-app-layout>