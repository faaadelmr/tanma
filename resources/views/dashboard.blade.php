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
                                        <td class="text-xs sm:text-sm">{{ $performer['average_score'] }} Poin</td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    @else
                        {{ is_numeric($value) ? number_format($value, 0, ',', '.') : $value }}
                    @endif
                </div>
                @if ($metric === 'total_reports') 
                @else
                <div class="text-xs stat-desc">{{ now()->format('F Y') }}</div>
                @endif
            </div>
            @endforeach
        </div>

        <!-- Time Period Selector -->
        <div class="flex gap-2 mb-4">
            <select id="timePeriod" class="select select-bordered w-full max-w-xs" onchange="updateCharts(this.value)">
                <option value="week">Mingguan</option>
                <option value="month">Bulanan</option>
                <option value="year">Tahunan</option>
                <option value="custom">Pilih Tanggal</option>
            </select>

            <div id="dateRangeInputs" class="flex gap-2" style="display: none;">
                <input type="date" id="startDate" class="input input-bordered" value="{{ date('Y-m-d') }}">
                <input type="date" id="endDate" class="input input-bordered" value="{{ date('Y-m-d') }}">
                <button onclick="updateCustomRange()" class="btn btn-primary">Kirim</button>
            </div>
        </div>

        <!-- Category Groups Tabs -->
        <div class="space-y-4">
            <div class="overflow-x-auto whitespace-nowrap tabs tabs-boxed">
                @foreach($groupedCategories as $groupName => $categories)
                <a class="tab tab-sm sm:tab-md {{ $loop->first ? 'tab-active' : '' }}"
                   onclick="switchCategoryGroup('{{ $groupName }}')">
                    {{ $groupName }}
                </a>
                @endforeach
            </div>

            <!-- Clickable Category Details -->
            @foreach($groupedCategories as $groupName => $categories)
            <div id="details-{{ $groupName }}" class="category-details {{ !$loop->first ? 'hidden' : '' }}">
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4">
                    <div class="p-3 rounded-lg shadow-md bg-base-100 cursor-pointer hover:shadow-lg transition-all ring-2 ring-primary"
                         onclick="filterCategory('{{ $groupName }}', 'all')">
                        <h3 class="text-sm font-semibold">Semua Kategori</h3>
                    </div>
                    @foreach($categories as $category)
                    <div class="p-3 rounded-lg shadow-md bg-base-100 cursor-pointer hover:shadow-lg transition-all" 
                         onclick="filterCategory('{{ $groupName }}', '{{ $category->name }}')">
                        <h3 class="text-sm font-semibold">{{ $category->name }}</h3>
                        <div class="mt-2 space-y-1 text-xs">
                            @if($category->has_batch)
                                <span class="badge badge-primary badge-outline">Batch</span>
                            @endif
                            @if($category->has_claim)
                                <span class="badge badge-primary badge-outline">Claim</span>
                            @endif
                            @if($category->has_sheet)
                                <span class="badge badge-primary badge-outline">Sheet</span>
                            @endif
                            @if($category->has_email)
                                <span class="badge badge-primary badge-outline">Email</span>
                            @endif
                            @if($category->has_form)
                                <span class="badge badge-primary badge-outline">Form</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        <!-- Charts Area -->
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let charts = {};
        let chartData = {};

        function createChart(groupName, data, selectedCategory) {
            const ctx = document.getElementById(`taskChart-${groupName}`).getContext('2d');
            
            if (charts[groupName]) {
                charts[groupName].destroy();
            }
            
            let filteredData = [];
            
            if (selectedCategory === 'all') {
                Object.entries(data).forEach(([category, categoryData]) => {
                    if (category.startsWith(groupName)) {
                        filteredData.push(...categoryData);
                    }
                });
            } else {
                filteredData = data[selectedCategory] || [];
            }

            const labels = [...new Set(filteredData.map(item => item.date))].sort();

            const totals = {
                batch: filteredData.reduce((sum, item) => sum + (Number(item.batch_count) || 0), 0),
                claim: filteredData.reduce((sum, item) => sum + (Number(item.claim_count) || 0), 0),
                sheet: filteredData.reduce((sum, item) => sum + (Number(item.sheet_count) || 0), 0),
                email: filteredData.reduce((sum, item) => sum + (Number(item.email) || 0), 0),
                form: filteredData.reduce((sum, item) => sum + (Number(item.form) || 0), 0)
            };

            const datasets = [
                {
                    label: `Batch (Total: ${totals.batch})`,
                    borderColor: '#9333EA',
                    backgroundColor: '#9333EA20',
                    data: labels.map(date => {
                        return filteredData
                            .filter(item => item.date === date)
                            .reduce((sum, item) => sum + (Number(item.batch_count) || 0), 0);
                    }),
                    tension: 0.4,
                    fill: true
                },
                {
                    label: `Claim (Total: ${totals.claim})`,
                    borderColor: '#3B82F6',
                    backgroundColor: '#3B82F620',
                    data: labels.map(date => {
                        return filteredData
                            .filter(item => item.date === date)
                            .reduce((sum, item) => sum + (Number(item.claim_count) || 0), 0);
                    }),
                    tension: 0.4,
                    fill: true
                },
                {
                    label: `Sheet (Total: ${totals.sheet})`,
                    borderColor: '#22C55E',
                    backgroundColor: '#22C55E20',
                    data: labels.map(date => {
                        return filteredData
                            .filter(item => item.date === date)
                            .reduce((sum, item) => sum + (Number(item.sheet_count) || 0), 0);
                    }),
                    tension: 0.4,
                    fill: true
                },
                {
                    label: `Email (Total: ${totals.email})`,
                    borderColor: '#EAB308',
                    backgroundColor: '#EAB30820',
                    data: labels.map(date => {
                        return filteredData
                            .filter(item => item.date === date)
                            .reduce((sum, item) => sum + (Number(item.email) || 0), 0);
                    }),
                    tension: 0.4,
                    fill: true
                },
                {
                    label: `Form (Total: ${totals.form})`,
                    borderColor: '#EC4899',
                    backgroundColor: '#EC489920',
                    data: labels.map(date => {
                        return filteredData
                            .filter(item => item.date === date)
                            .reduce((sum, item) => sum + (Number(item.form) || 0), 0);
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
                                    const categoryName = selectedCategory === 'all' ? 
                                        `${context.dataset.label.split(' (')[0]} - Multiple Categories` : 
                                        `${context.dataset.label.split(' (')[0]} - ${selectedCategory}`;
                                    return `${categoryName}: ${context.parsed.y}`;
                                },
                                afterLabel: function(context) {
                                    return `Date: ${context.label}`;
                                }
                            }
                        }
                    }
                }
            });
        }

        function filterCategory(groupName, category) {
            // Update visual selection
            document.querySelectorAll(`#details-${groupName} .bg-base-100`).forEach(el => {
                el.classList.remove('ring-2', 'ring-primary');
            });
            
            const selectedEl = Array.from(document.querySelectorAll(`#details-${groupName} .bg-base-100`))
                .find(el => el.querySelector('h3').textContent.trim() === (category === 'all' ? 'Semua Kategori' : category));
            
            if (selectedEl) {
                selectedEl.classList.add('ring-2', 'ring-primary');
            }
            
            // Filter and update chart data
            const filteredData = category === 'all' ? chartData : {
                [category]: chartData[category]
            };
            
            createChart(groupName, filteredData, category);
        }

        function switchCategoryGroup(groupName) {
            document.querySelectorAll('.category-group').forEach(el => el.classList.add('hidden'));
            document.getElementById(`group-${groupName}`).classList.remove('hidden');
            
            document.querySelectorAll('.category-details').forEach(el => el.classList.add('hidden'));
            document.getElementById(`details-${groupName}`).classList.remove('hidden');
            
            document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('tab-active'));
            event.target.classList.add('tab-active');
        }

        function updateCharts(period) {
            document.querySelectorAll('canvas').forEach(canvas => {
                canvas.style.opacity = '0.5';
            });

            fetch(`/chart-data/${period}`)
                .then(response => response.json())
                .then(data => {
                    document.querySelectorAll('canvas').forEach(canvas => {
                        canvas.style.opacity = '1';
                    });
                    
                    if (Object.keys(data).length > 0) {
                        chartData = data;
                        Object.keys(data).forEach(groupName => {
                            if (document.getElementById(`taskChart-${groupName}`)) {
                                createChart(groupName, data, 'all');
                            }
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.querySelectorAll('canvas').forEach(canvas => {
                        canvas.style.opacity = '1';
                    });
                });
        }

        function updateCustomRange() {
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            
            fetch(`/chart-data/custom/${startDate}/${endDate}`)
                .then(response => response.json())
                .then(data => {
                    if (Object.keys(data).length > 0) {
                        chartData = data;
                        Object.keys(data).forEach(groupName => {
                            if (document.getElementById(`taskChart-${groupName}`)) {
                                createChart(groupName, data, 'all');
                            }
                        });
                    }
                });
        }

        document.addEventListener('DOMContentLoaded', () => {
            const initialPeriod = document.getElementById('timePeriod').value;
            updateCharts(initialPeriod);

            document.getElementById('timePeriod').addEventListener('change', function(e) {
                const dateRangeInputs = document.getElementById('dateRangeInputs');
                dateRangeInputs.style.display = e.target.value === 'custom' ? 'flex' : 'none';
                
                if (e.target.value !== 'custom') {
                    updateCharts(e.target.value);
                }
            });
        });
    </script>
</x-app-layout>