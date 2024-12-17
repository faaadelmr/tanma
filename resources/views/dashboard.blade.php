<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Metrics Carousel -->
            <div class="flex justify-end items-center space-x-4">
                <select id="dateRange" class="select select-bordered w-full max-w-xs"
                    onchange="updateDashboard(this.value)">
                    <option value="week" {{ $dateRange === 'week' ? 'selected' : '' }}>Minggu ini</option>
                    <option value="month" {{ $dateRange === 'month' ? 'selected' : '' }}>Bulan ini</option>
                    <option value="three_months" {{ $dateRange === 'three_months' ? 'selected' : '' }}>3 Bulan yang lalu
                    </option>
                    <option value="six_months" {{ $dateRange === 'six_months' ? 'selected' : '' }}>6 Bulan yang lalu
                    </option>
                    <option value="year" {{ $dateRange === 'year' ? 'selected' : '' }}>Tahun ini</option>
                </select>
                <button class="btn btn-primary" onclick="refreshData()">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Update
                </button>
            </div>
            <div class="carousel w-full mb-8 py-3 rounded-box">
                @foreach (collect($metrics)->chunk(3) as $index => $metricsChunk)
                    <div id="slide{{ $index }}" class="carousel-item relative w-full">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 w-full px-4">
                            @foreach ($metricsChunk as $category => $metric)
                                <div class="card bg-base-100 hover:bg-base-200 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 cursor-pointer backdrop-blur-sm"
                                    onclick="loadCategoryChart('{{ $category }}')">
                                    <div class="card-body">
                                        <h3 class="card-title text-xl font-bold text-primary">{{ $category }}</h3>
                                        <div class="stats bg-base-200/50 shadow-inner rounded-xl">
                                            <div class="stat p-4">
                                                <div class="stat-title font-medium opacity-70">Current Total</div>
                                                <div class="stat-value text-3xl my-2">
                                                    {{ number_format($metric['current_total']) }}</div>
                                                <div class="stat-desc flex items-center gap-3 mt-2">
                                                    @php
                                                        $percentageChange = $metric['percentage_change'];
                                                        $isIncreasing =
                                                            $metric['current_total'] > $metric['previous_total'];
                                                        $trendClass = $isIncreasing ? 'text-success' : 'text-error';
                                                        $badgeClass = $isIncreasing ? 'badge-success' : 'badge-error';
                                                        $trendArrow = $isIncreasing ? '↑' : '↓';

                                                        // Format percentage display
                                                        $formattedPercentage =
                                                            $percentageChange == 0
                                                                ? '0'
                                                                : ($percentageChange > 0
                                                                    ? '+' . $percentageChange
                                                                    : $percentageChange);
                                                    @endphp
                                                    <div class="flex items-center gap-2 {{ $trendClass }} font-bold">
                                                        <span class="text-lg">{{ $formattedPercentage }}%</span>
                                                        <span class="badge badge-lg {{ $badgeClass }} gap-1">
                                                            {{ $trendArrow }}
                                                        </span>
                                                    </div>
                                                    <span class="text-base-content/60">vs previous</span>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="absolute flex justify-between transform -translate-y-1/2 left-5 right-5 top-1/2">
                            <a href="#slide{{ $index - 1 }}"
                                class="btn btn-circle {{ $index === 0 ? 'invisible' : '' }}">❮</a>
                            <a href="#slide{{ $index + 1 }}"
                                class="btn btn-circle {{ $index === collect($metrics)->chunk(3)->count() - 1 ? 'invisible' : '' }}">❯</a>
                        </div>
                    </div>
                @endforeach
            </div>
            <!-- Category Chart Section -->
            <div class=" card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h3 class="card-title" id="chartTitle">Overall Performance</h3>
                    <div class="w-full h-[400px]">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        let currentChart;
        const colors = [
            'rgb(75, 192, 192)',
            'rgb(255, 99, 132)',
            'rgb(255, 205, 86)',
            'rgb(54, 162, 235)',
            'rgb(153, 102, 255)'
        ];

        function loadCategoryChart(category) {
            const chartData = @json($chartData);
            const categoryData = {
                labels: chartData.labels,
                datasets: chartData.datasets.filter(dataset => dataset.label === category)
            };

            if (currentChart) {
                currentChart.destroy();
            }

            const ctx = document.getElementById('categoryChart').getContext('2d');
            document.getElementById('chartTitle').textContent = `${category} Performance`;

            currentChart = new Chart(ctx, {
                type: 'line',
                data: categoryData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `Total: ${context.parsed.y.toLocaleString()}`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: value => value.toLocaleString()
                            }
                        }
                    }
                }
            });
        }

        function updateDashboard(range) {
            window.location.href = `${window.location.pathname}?date_range=${range}`;
        }

        function refreshData() {
            window.location.reload();
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadCategoryChart('Overall');
        });
    </script>
</x-app-layout>
