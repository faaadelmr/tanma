<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-semibold leading-tight text-primary">
                {{ __('Dashboard') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div id="caraousel" class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white rounded-lg shadow-sm">
                <div class="p-4 sm:p-6">
                    <!-- Date Picker -->
                    <div class="mb-4">
                        <div class="flex flex-col space-y-4">
                            <div class="flex gap-4 justify-between items-start">
                                <form method="GET" class="flex-1 p-4 bg-white rounded-lg shadow-sm" id="dateForm">
                                    <label for="date" class="flex gap-1 items-center mb-1 text-sm font-medium text-gray-700">
                                        <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        Pilih Tanggal
                                    </label>
                                    <input type="date" name="date" id="date" value="<?php echo $selectedDate->format('Y-m-d'); ?>"
                                        class="w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-1" onchange="this.form.submit()">
                                </form>

                                <form action="{{ route('daily-reports.export') }}" method="GET" class="flex-1 p-4 bg-white rounded-lg shadow-sm">
                                    @csrf
                                    <div class="flex gap-4">
                                        <div class="group hover:scale-[1.01] transition-transform flex-1">
                                            <label class="flex gap-1 items-center mb-1 text-sm font-medium text-gray-700">
                                                <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                Tanggal Mulai
                                            </label>
                                            <input type="date" name="start_date" class="w-full rounded-md border-gray-300 cursor-pointer focus:border-indigo-500 focus:ring-1" value="{{ request('start_date', now()->format('Y-m-d')) }}" required>
                                        </div>

                                        <div class="group hover:scale-[1.01] transition-transform flex-1">
                                            <label class="flex gap-1 items-center mb-1 text-sm font-medium text-gray-700">
                                                <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                Tanggal Akhir
                                            </label>
                                            <input type="date" name="end_date" class="w-full rounded-md border-gray-300 cursor-pointer focus:border-indigo-500 focus:ring-1" value="{{ request('end_date', now()->format('Y-m-d')) }}" required>
                                        </div>

                                        <div class="flex items-end">
                                            <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-indigo-500 transition-all hover:scale-[1.02]">
                                                <svg class="mr-1.5 w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                </svg>
                                                Export to Excel
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- Carousel -->
                    <div id="carousel" class="relative">
                        <!-- Navigation buttons - Hidden on mobile, visible on larger screens -->
                        <button onclick="prevSlide()"
                            class="hidden absolute left-0 top-1/2 z-10 p-2 text-white rounded-r-lg -translate-y-1/2 sm:block bg-gray-800/50 hover:bg-gray-800/75">
                            <svg class="w-4 h-4 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>

                        <button onclick="nextSlide()"
                            class="hidden absolute right-0 top-1/2 z-10 p-2 text-white rounded-l-lg -translate-y-1/2 sm:block bg-gray-800/50 hover:bg-gray-800/75">
                            <svg class="w-4 h-4 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </button>

                        <div class="overflow-hidden rounded-xl">
                            <?php
                            // Group and sort the data
                            $groupedComparisons = [];
                            foreach ($comparisons as $categoryName => $data) {
                                $prefix = explode(' ', $categoryName)[0];
                                $groupedComparisons[$prefix][$categoryName] = $data;
                            }
                            ksort($groupedComparisons); // Sort groups alphabetically
                            ?>

                            <div id="slides" class="flex transition-transform duration-500">
                                <?php foreach($groupedComparisons as $groupName => $categories): ?>
                                <?php ksort($categories); // Sort items within group alphabetically ?>
                                <div class="flex-shrink-0 p-3 w-full sm:p-6">
                                    <h2 class="mb-4 text-lg font-bold text-center sm:text-2xl sm:mb-6">
                                        <?php echo $groupName; ?> Data</h2>
                                    <?php foreach($categories as $categoryName => $data): ?>
                                    <div class="mb-6 sm:mb-8">
                                        <h3 class="mb-3 text-base font-semibold text-gray-700 sm:text-xl sm:mb-4">
                                            <?php echo $categoryName; ?></h3>
                                        <div class="grid grid-cols-2 gap-2 md:grid-cols-4 sm:gap-4">
                                            <div class="p-2 bg-blue-50 rounded-lg sm:p-4">
                                                <h3 class="text-sm font-semibold text-blue-600 sm:text-lg">Hari ini</h3>
                                                <p class="text-xl font-bold text-blue-700 sm:text-3xl">
                                                    <?php echo number_format($data['current_total']); ?></p>
                                            </div>
                                            <div class="p-2 bg-green-50 rounded-lg sm:p-4">
                                                <h3 class="flex gap-2 items-center text-sm font-semibold text-green-600 sm:text-lg">vs Kemarin <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-green-100 rounded-full">{{ number_format($data['previous_day_total']) }}</span></h3>
                                                </h3>
                                                <p class="text-xl sm:text-3xl font-bold <?php echo $data['day_change'] >= 0 ? 'text-green-600' : 'text-red-600'; ?>">
                                                    <?php echo $data['day_change']; ?>%
                                                </p>

                                            </div>
                                            <div class="p-2 bg-purple-50 rounded-lg sm:p-4">
                                                <h3 class="text-sm font-semibold text-purple-600 sm:text-lg">vs Minggu lalu
                                                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-purple-100 rounded-full">{{ number_format($data['previous_week_total']) }}</span>
                                                    </h3>
                                                <p class="text-xl sm:text-3xl font-bold <?php echo $data['week_change'] >= 0 ? 'text-green-600' : 'text-red-600'; ?>">
                                                    <?php echo $data['week_change']; ?>%
                                                </p>
                                            </div>
                                            <div class="p-2 bg-orange-50 rounded-lg sm:p-4">
                                                <h3 class="text-sm font-semibold text-orange-600 sm:text-lg">vs Bulan lalu
                                                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-orange-100 rounded-full">{{ number_format($data['previous_month_total']) }}</span>
                                                    </h3>
                                                <p class="text-xl sm:text-3xl font-bold <?php echo $data['month_change'] >= 0 ? 'text-green-600' : 'text-red-600'; ?>">
                                                    <?php echo $data['month_change']; ?>%
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <!-- Indicators -->
                        <div class="flex gap-2 justify-center mt-4">
                            <?php $index = 0; ?>
                            @foreach ($groupedComparisons as $groupName => $categories)
                                <button onclick="goToSlide(<?php echo $index; ?>)"
                                    class="w-3 h-3 bg-gray-300 rounded-full transition-all duration-300 hover:bg-blue-400"
                                    id="indicator-<?php echo $index; ?>">
                                </button>
                                <?php $index++; ?>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        let currentSlide = parseInt(localStorage.getItem('currentCarouselSlide')) || 0;
        const slides = document.getElementById('slides');
        const totalSlides = document.querySelectorAll('#slides > div').length;

        function updateCarousel() {
            slides.style.transform = `translateX(-${currentSlide * 100}%)`;
            updateIndicators();
            localStorage.setItem('currentCarouselSlide', currentSlide);
        }

        function updateIndicators() {
            for (let i = 0; i < totalSlides; i++) {
                const indicator = document.getElementById(`indicator-${i}`);
                if (i === currentSlide) {
                    indicator.classList.add('bg-blue-600');
                    indicator.classList.remove('bg-gray-300');
                } else {
                    indicator.classList.remove('bg-blue-600');
                    indicator.classList.add('bg-gray-300');
                }
            }
        }

        function nextSlide() {
            currentSlide = (currentSlide + 1) % totalSlides;
            updateCarousel();
        }

        function prevSlide() {
            currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
            updateCarousel();
        }

        function goToSlide(index) {
            currentSlide = index;
            updateCarousel();
        }
        // Auto-play functionality
        let autoplayInterval = setInterval(nextSlide, 30000);

        // Pause auto-play on hover
        const carousel = document.getElementById('carousel');
        carousel.addEventListener('mouseenter', () => clearInterval(autoplayInterval));
        carousel.addEventListener('mouseleave', () => {
            autoplayInterval = setInterval(nextSlide, 60000);
        });

        // Initialize carousel with saved position
        updateCarousel();

        // Add loading animation when date changes
        document.getElementById('date').addEventListener('change', function() {
            document.body.style.cursor = 'wait';
            const overlay = document.createElement('div');
            overlay.style.position = 'fixed';
            overlay.style.top = '0';
            overlay.style.left = '0';
            overlay.style.width = '100%';
            overlay.style.height = '100%';
            overlay.style.backgroundColor = 'rgba(255, 255, 255, 0.7)';
            overlay.style.display = 'flex';
            overlay.style.justifyContent = 'center';
            overlay.style.alignItems = 'center';
            overlay.style.zIndex = '9999';
            overlay.innerHTML =
                '<div class="w-12 h-12 rounded-full border-t-2 border-b-2 border-blue-500 animate-spin"></div>';
            document.body.appendChild(overlay);
            localStorage.setItem('currentCarouselSlide', currentSlide);
            this.form.submit();
        });
    </script>

</x-app-layout>
