<x-app-layout>
    <x-slot name="header">
        <h2 class="text-primary font-semibold text-2xl leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>


    <div class="py-6 sm:py-12">
        <div id="caraousel" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-4 sm:p-6">
                    <!-- Date Picker -->
                    <div class="mb-4 sm:mb-6">
                        <form method="GET" class="flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-4"
                            id="dateForm">
                            <label for="date" class="text-sm sm:text-base">Select Date</label>
                            <input type="date" name="date" id="date" value="<?php echo $selectedDate->format('Y-m-d'); ?>"
                                class="w-full sm:w-auto rounded-md border-gray-300" onchange="this.form.submit()">
                        </form>
                    </div>

                    <!-- Carousel -->
                    <div id="carousel" class="relative">
                        <!-- Navigation buttons - Hidden on mobile, visible on larger screens -->
                        <button onclick="prevSlide()"
                            class="hidden sm:block absolute left-0 top-1/2 -translate-y-1/2 z-10 bg-gray-800/50 hover:bg-gray-800/75 text-white rounded-r-lg p-2">
                            <svg class="w-4 h-4 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>

                        <button onclick="nextSlide()"
                            class="hidden sm:block absolute right-0 top-1/2 -translate-y-1/2 z-10 bg-gray-800/50 hover:bg-gray-800/75 text-white rounded-l-lg p-2">
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
                                <div class="w-full flex-shrink-0 p-3 sm:p-6">
                                    <h2 class="text-lg sm:text-2xl font-bold mb-4 sm:mb-6 text-center">
                                        <?php echo $groupName; ?> Data</h2>
                                    <?php foreach($categories as $categoryName => $data): ?>
                                    <div class="mb-6 sm:mb-8">
                                        <h3 class="text-base sm:text-xl font-semibold mb-3 sm:mb-4 text-gray-700">
                                            <?php echo $categoryName; ?></h3>
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 sm:gap-4">
                                            <div class="bg-blue-50 p-2 sm:p-4 rounded-lg">
                                                <h3 class="text-sm sm:text-lg font-semibold text-blue-600">Hari ini</h3>
                                                <p class="text-xl sm:text-3xl font-bold text-blue-700">
                                                    <?php echo number_format($data['current_total']); ?></p>
                                            </div>
                                            <div class="bg-green-50 p-2 sm:p-4 rounded-lg">
                                                <h3 class="text-sm sm:text-lg font-semibold text-green-600">vs Kemarin
                                                </h3>
                                                <p class="text-xl sm:text-3xl font-bold <?php echo $data['day_change'] >= 0 ? 'text-green-600' : 'text-red-600'; ?>">
                                                    <?php echo $data['day_change']; ?>%
                                                </p>
                                            </div>
                                            <div class="bg-purple-50 p-2 sm:p-4 rounded-lg">
                                                <h3 class="text-sm sm:text-lg font-semibold text-purple-600">vs Minggu
                                                    lalu</h3>
                                                <p class="text-xl sm:text-3xl font-bold <?php echo $data['week_change'] >= 0 ? 'text-green-600' : 'text-red-600'; ?>">
                                                    <?php echo $data['week_change']; ?>%
                                                </p>
                                            </div>
                                            <div class="bg-orange-50 p-2 sm:p-4 rounded-lg">
                                                <h3 class="text-sm sm:text-lg font-semibold text-orange-600">vs Bulan
                                                    lalu</h3>
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
                        <div class="flex justify-center mt-4 gap-2">
                            <?php $index = 0; ?>
                            @foreach ($groupedComparisons as $groupName => $categories)
                                <button onclick="goToSlide(<?php echo $index; ?>)"
                                    class="h-3 w-3 bg-gray-300 rounded-full transition-all duration-300 hover:bg-blue-400"
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
                '<div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>';
            document.body.appendChild(overlay);
            localStorage.setItem('currentCarouselSlide', currentSlide);
            this.form.submit();
        });
    </script>

</x-app-layout>
