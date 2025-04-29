<div>

    <div>
        <div class="space-y-6">
            <!-- Period selector with improved responsiveness -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <h2 class="text-lg font-medium">Tempoh Analisis</h2>
                    <div class="flex flex-wrap gap-2">
                        <a href="?period=month" aria-label="View data for 30 days"
                            class="px-3 py-1.5 text-sm rounded-full transition-colors {{ $period === 'month' ? 'bg-primary-500 text-white' : 'bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300' }}">
                            30 Hari
                        </a>
                        <a href="?period=quarter" aria-label="View data for 3 months"
                            class="px-3 py-1.5 text-sm rounded-full transition-colors {{ $period === 'quarter' ? 'bg-primary-500 text-white' : 'bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300' }}">
                            3 Bulan
                        </a>
                        <a href="?period=year" aria-label="View data for 12 months"
                            class="px-3 py-1.5 text-sm rounded-full transition-colors {{ $period === 'year' ? 'bg-primary-500 text-white' : 'bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300' }}">
                            12 Bulan
                        </a>
                    </div>
                </div>
            </div>

            <!-- Completely revamped grid layout -->
            <div class="grid grid-cols-1 gap-6">
                <!-- Member Growth Chart - always full width -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 gap-2">
                        <h2 class="text-lg font-medium">Pertumbuhan Keahlian</h2>
                        <div class="text-sm text-gray-500">
                            @if ($period === 'month')
                                30 hari terakhir
                            @elseif($period === 'quarter')
                                3 bulan terakhir
                            @elseif($period === 'year')
                                12 bulan terakhir
                            @endif
                        </div>
                    </div>
                    <div id="memberGrowthChart" style="min-height: 300px; width: 100%"></div>
                </div>

                <!-- Payment Trends Chart - always full width -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 gap-2">
                        <h2 class="text-lg font-medium">Trend Pembayaran</h2>
                        <div class="text-sm text-gray-500">
                            @if ($period === 'month')
                                30 hari terakhir
                            @elseif($period === 'quarter')
                                3 bulan terakhir
                            @elseif($period === 'year')
                                12 bulan terakhir
                            @endif
                        </div>
                    </div>
                    <div id="paymentTrendsChart" style="min-height: 300px; width: 100%"></div>
                </div>

                <!-- Small charts container - separate grid for small charts -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Death Records Chart -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                        <div class="flex items-start justify-between mb-4">
                            <h2 class="text-lg font-medium">Rekod Kematian</h2>
                        </div>
                        <div id="deathRecordsChart" style="min-height: 300px; width: 100%"></div>
                    </div>

                    <!-- Age Distribution Chart -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                        <div class="flex items-start justify-between mb-4">
                            <h2 class="text-lg font-medium">Taburan Umur Ahli dan Tanggungan</h2>
                        </div>
                        <div id="ageDistributionChart" style="min-height: 300px; width: 100%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Include ApexCharts from CDN -->
        <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.41.0/dist/apexcharts.min.js" defer></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Load ApexCharts and initialize
                loadApexChartsLibrary();
            });

            function loadApexChartsLibrary() {
                // Check if ApexCharts is already loaded
                if (typeof ApexCharts === 'undefined') {
                    const script = document.createElement('script');
                    script.src = 'https://cdn.jsdelivr.net/npm/apexcharts@3.41.0/dist/apexcharts.min.js';
                    script.onload = function() {
                        initializeAllCharts();
                    };
                    script.onerror = function() {
                        console.error('Failed to load ApexCharts');
                        showChartErrors();
                    };
                    document.head.appendChild(script);
                } else {
                    initializeAllCharts();
                }
            }

            function initializeAllCharts() {
                // Set up theme change detection
                setupThemeChangeListener();

                // Get chart configs
                const configs = getChartConfigs();

                // Initialize each chart
                initializeChart('memberGrowthChart', getMemberGrowthConfig(configs.memberGrowth));
                initializeChart('paymentTrendsChart', getPaymentTrendsConfig(configs.paymentTrends));
                initializeChart('deathRecordsChart', getDeathRecordsConfig(configs.deathRecords));
                initializeChart('ageDistributionChart', getAgeDistributionConfig(configs.ageDistribution));

                // Add toolbar style fixes
                fixToolbarStyles();

                // Handle resize events
                setupResizeHandler();
            }

            // Show error messages when charts can't be rendered
            function showChartErrors() {
                const chartContainers = ['#memberGrowthChart', '#paymentTrendsChart', '#deathRecordsChart', '#ageDistributionChart'];
                chartContainers.forEach(container => {
                    const element = document.querySelector(container);
                    if (element) {
                        element.innerHTML = `
                            <div class="flex flex-col items-center justify-center h-full text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <p>Tidak dapat memuat carta</p>
                            </div>
                        `;
                    }
                });
            }

            // Helper function to initialize a chart
            function initializeChart(containerId, config) {
                // Clear existing chart
                const container = document.getElementById(containerId);
                if (!container) return;

                container.innerHTML = '';

                // Create and store chart instance
                window[containerId] = new ApexCharts(container, config);
                window[containerId].render();
            }

            // Set up a mutation observer to watch for theme changes
            function setupThemeChangeListener() {
                const observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                            // Reinitialize charts when theme changes
                            initializeAllCharts();
                        }
                    });
                });

                // Start observing document for theme class changes
                observer.observe(document.documentElement, {
                    attributes: true,
                    attributeFilter: ['class']
                });
            }

            // Debounce function to limit how often a function is called
            function debounce(func, wait) {
                let timeout;
                return function() {
                    const context = this, args = arguments;
                    clearTimeout(timeout);
                    timeout = setTimeout(function() {
                        func.apply(context, args);
                    }, wait);
                };
            }

            // Set up resize handler
            function setupResizeHandler() {
                window.addEventListener('resize', debounce(function() {
                    // Completely reinitialize all charts on resize
                    initializeAllCharts();
                }, 250));
            }

            // Fix toolbar styles for dark mode
            function fixToolbarStyles() {
                if (!document.documentElement.classList.contains('dark')) return;

                // Fix chart toolbar colors in dark mode
                setTimeout(() => {
                    document.querySelectorAll('.apexcharts-toolbar svg *, .apexcharts-toolbar line').forEach(el => {
                        el.style.stroke = '#e5e7eb';
                    });

                    document.querySelectorAll('.apexcharts-menu').forEach(menu => {
                        menu.style.background = '#374151';
                        menu.style.border = '1px solid #4b5563';
                    });

                    document.querySelectorAll('.apexcharts-menu-item').forEach(item => {
                        item.style.color = '#e5e7eb';
                    });
                }, 300);
            }

            // Get base chart configs with theme support
            function getChartConfigs() {
                const isDarkMode = document.documentElement.classList.contains('dark');
                const textColor = isDarkMode ? '#e5e7eb' : '#374151';
                const borderColor = isDarkMode ? '#374151' : '#e0e0e0';

                // Common configuration for all charts
                const commonConfig = {
                    chart: {
                        fontFamily: 'inherit',
                        background: 'transparent',
                        toolbar: {
                            show: true,
                            tools: {
                                download: true,
                                selection: false,
                                zoom: false,
                                zoomin: false,
                                zoomout: false,
                                pan: false,
                                reset: false
                            }
                        },
                        animations: {
                            enabled: true,
                            easing: 'easeinout',
                            speed: 500
                        },
                        redrawOnWindowResize: true,
                        redrawOnParentResize: true
                    },
                    theme: {
                        mode: isDarkMode ? 'dark' : 'light',
                    },
                    colors: ['#3b82f6', '#10b981', '#ef4444', '#f59e0b', '#8b5cf6'],
                    states: {
                        hover: {
                            filter: {
                                type: 'darken',
                                value: 0.1
                            }
                        },
                        active: {
                            filter: {
                                type: 'darken',
                                value: 0.2
                            }
                        }
                    },
                    tooltip: {
                        theme: isDarkMode ? 'dark' : 'light',
                        shared: true,
                        intersect: false,
                    },
                    grid: {
                        borderColor: borderColor,
                        strokeDashArray: 5,
                        padding: {
                            top: 0,
                            right: 10,
                            bottom: 0,
                            left: 10
                        },
                    },
                    foreColor: textColor
                };

                return {
                    memberGrowth: { ...commonConfig },
                    paymentTrends: { ...commonConfig },
                    deathRecords: { ...commonConfig },
                    ageDistribution: { ...commonConfig }
                };
            }

            // Member Growth Chart Configuration
            function getMemberGrowthConfig(baseConfig) {
                const memberData = @json($memberGrowthData);

                if (!memberData || memberData.length === 0) {
                    return showEmptyState('memberGrowthChart');
                }

                return {
                    ...baseConfig,
                    chart: {
                        ...baseConfig.chart,
                        type: 'area',
                        height: 300
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 2
                    },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.7,
                            opacityTo: 0.2,
                            stops: [0, 90, 100]
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    series: [{
                        name: 'Ahli Baru',
                        data: memberData.map(item => item.value)
                    }],
                    xaxis: {
                        categories: memberData.map(item => item.label),
                        labels: {
                            rotate: -45,
                            style: {
                                fontSize: '10px'
                            },
                            trim: true,
                            maxHeight: 120
                        },
                        tooltip: {
                            enabled: false
                        }
                    },
                    yaxis: {
                        title: {
                            text: 'Jumlah Ahli',
                            style: {
                                fontSize: '12px'
                            }
                        },
                        labels: {
                            formatter: function(val) {
                                return val.toFixed(0);
                            }
                        }
                    },
                    markers: {
                        size: 3,
                        strokeWidth: 0,
                        hover: {
                            size: 4
                        }
                    },
                    tooltip: {
                        ...baseConfig.tooltip,
                        y: {
                            formatter: function(val) {
                                return val + ' ahli';
                            }
                        }
                    }
                };
            }

            // Payment Trends Chart Configuration
            function getPaymentTrendsConfig(baseConfig) {
                const paymentData = @json($paymentTrendsData);
                const isDarkMode = document.documentElement.classList.contains('dark');

                if (!paymentData || paymentData.length === 0) {
                    return showEmptyState('paymentTrendsChart');
                }

                return {
                    ...baseConfig,
                    chart: {
                        ...baseConfig.chart,
                        type: 'bar',
                        height: 300
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: 4,
                            columnWidth: '60%',
                            dataLabels: {
                                position: 'top'
                            }
                        }
                    },
                    colors: ['#10b981'],
                    dataLabels: {
                        enabled: true,
                        offsetY: -20,
                        style: {
                            fontSize: '10px',
                            colors: [isDarkMode ? '#e5e7eb' : '#304758']
                        },
                        formatter: function(val) {
                            return 'RM ' + val.toFixed(0);
                        }
                    },
                    series: [{
                        name: 'Jumlah Pembayaran',
                        data: paymentData.map(item => item.value)
                    }],
                    xaxis: {
                        categories: paymentData.map(item => item.label),
                        labels: {
                            rotate: -45,
                            style: {
                                fontSize: '10px'
                            },
                            trim: true,
                            maxHeight: 120
                        },
                        tooltip: {
                            enabled: false
                        }
                    },
                    yaxis: {
                        title: {
                            text: 'Jumlah (RM)',
                            style: {
                                fontSize: '12px'
                            }
                        },
                        labels: {
                            formatter: function(val) {
                                return 'RM ' + val.toFixed(0);
                            }
                        }
                    },
                    tooltip: {
                        ...baseConfig.tooltip,
                        y: {
                            formatter: function(val) {
                                return 'RM ' + val.toFixed(2);
                            }
                        }
                    }
                };
            }

            // Death Records Chart Configuration
            function getDeathRecordsConfig(baseConfig) {
                const deathData = @json($deathRecordsData);

                if (!deathData || !deathData.labels || deathData.labels.length === 0) {
                    return showEmptyState('deathRecordsChart');
                }

                return {
                    ...baseConfig,
                    chart: {
                        ...baseConfig.chart,
                        type: 'bar',
                        height: 300,
                        stacked: true
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            borderRadius: 4,
                            columnWidth: '70%'
                        }
                    },
                    colors: ['#ef4444', '#7c3aed'],
                    dataLabels: {
                        enabled: false
                    },
                    series: [{
                        name: 'Ahli',
                        data: deathData.memberDeaths
                    }, {
                        name: 'Tanggungan',
                        data: deathData.dependentDeaths
                    }],
                    xaxis: {
                        categories: deathData.labels,
                        labels: {
                            rotate: -45,
                            style: {
                                fontSize: '10px'
                            },
                            trim: true,
                            maxHeight: 120
                        },
                        tooltip: {
                            enabled: false
                        }
                    },
                    yaxis: {
                        title: {
                            text: 'Jumlah',
                            style: {
                                fontSize: '12px'
                            }
                        },
                        labels: {
                            formatter: function(val) {
                                return val.toFixed(0);
                            }
                        }
                    },
                    legend: {
                        position: 'top',
                        horizontalAlign: 'left',
                        offsetY: 0,
                        fontSize: '12px'
                    },
                    fill: {
                        opacity: 1
                    }
                };
            }

            // Age Distribution Chart Configuration
            function getAgeDistributionConfig(baseConfig) {
                const ageData = @json($ageDistributionData);

                if (!ageData || ageData.length === 0) {
                    return showEmptyState('ageDistributionChart');
                }

                return {
                    ...baseConfig,
                    chart: {
                        ...baseConfig.chart,
                        type: 'donut',
                        height: 300
                    },
                    colors: ['#0ea5e9', '#14b8a6', '#f59e0b', '#8b5cf6', '#ef4444', '#3b82f6', '#10b981'],
                    series: ageData.map(item => item.count),
                    labels: ageData.map(item => item.range + ' tahun'),
                    legend: {
                        position: 'bottom',
                        horizontalAlign: 'center',
                        offsetY: 5,
                        fontSize: '12px',
                        markers: {
                            width: 8,
                            height: 8,
                            radius: 8
                        },
                        itemMargin: {
                            horizontal: 8,
                            vertical: 2
                        }
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '70%',
                                labels: {
                                    show: true,
                                    name: {
                                        show: true,
                                        fontSize: '14px'
                                    },
                                    value: {
                                        show: true,
                                        fontSize: '14px',
                                        formatter: function(val) {
                                            return val + ' orang';
                                        }
                                    },
                                    total: {
                                        show: true,
                                        label: 'Jumlah',
                                        fontSize: '14px',
                                        formatter: function(w) {
                                            return w.globals.seriesTotals.reduce((a, b) => a + b, 0) + ' orang';
                                        }
                                    }
                                }
                            }
                        }
                    },
                    tooltip: {
                        ...baseConfig.tooltip,
                        y: {
                            formatter: function(val) {
                                return val + ' orang';
                            }
                        }
                    }
                };
            }

            // Helper function to show empty state
            function showEmptyState(containerId) {
                const container = document.getElementById(containerId);
                if (container) {
                    container.innerHTML = `
                        <div class="flex items-center justify-center h-full text-gray-500">
                            <p>Tiada data untuk dipaparkan</p>
                        </div>
                    `;
                }
                return {};
            }

            // Listen for Livewire updates
            document.addEventListener('livewire:initialized', function() {
                Livewire.on('forceRefresh', () => {
                    window.location.reload();
                });
            });
        </script>

    </div>
</div>
