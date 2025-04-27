<x-filament-panels::page>
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

            <!-- Improved responsive grid layout for charts -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                <!-- Member Growth Chart - full width on all screens -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 col-span-1 md:col-span-2">
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
                    <!-- Responsive height for chart -->
                    <div id="memberGrowthChart" class="w-full h-64 sm:h-80 md:h-96"></div>
                </div>

                <!-- Payment Trends Chart - full width on all screens -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 col-span-1 md:col-span-2">
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
                    <!-- Responsive height for chart -->
                    <div id="paymentTrendsChart" class="w-full h-64 sm:h-80 md:h-96"></div>
                </div>

                <!-- Death Records Chart - one column on small screens, two columns on larger screens -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                    <div class="flex items-start justify-between mb-4">
                        <h2 class="text-lg font-medium">Rekod Kematian</h2>
                    </div>
                    <!-- Responsive height for chart with reduced height -->
                    <div id="deathRecordsChart" class="w-full h-52 sm:h-60"></div>
                </div>

                <!-- Age Distribution Chart - one column on small screens, two columns on larger screens -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                    <div class="flex items-start justify-between mb-4">
                        <h2 class="text-lg font-medium">Taburan Umur Ahli</h2>
                    </div>
                    <!-- Responsive height for chart - matched with death records chart -->
                    <div id="ageDistributionChart" class="w-full h-52 sm:h-60"></div>
                </div>
            </div>
        </div>

        <!-- Include ApexCharts from CDN -->
        <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.41.0/dist/apexcharts.min.js" defer></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Check if ApexCharts is already loaded
                if (typeof ApexCharts === 'undefined') {
                    const script = document.createElement('script');
                    script.src = 'https://cdn.jsdelivr.net/npm/apexcharts@3.41.0/dist/apexcharts.min.js';
                    script.integrity = 'sha384-OMKlJP6k8LIfzCC8/BgVvp1+PKJ8GVhNuUMPQM0XvRPvKIgSSLEGTqGZjLGIfMgB';
                    script.crossOrigin = 'anonymous';
                    script.onload = function() {
                        initializeCharts();
                        // Add the CSS fix for dark mode toolbar
                        addToolbarStyle();
                    };
                    script.onerror = function() {
                        console.error('Failed to load ApexCharts');
                        showChartErrors();
                    };
                    document.head.appendChild(script);
                } else {
                    initializeCharts();
                    // Add the CSS fix for dark mode toolbar
                    addToolbarStyle();
                }

                // Set up theme change detection
                setupThemeChangeListener();

                // Handle window resize events
                window.addEventListener('resize', debounce(function() {
                    // Only re-render if charts exist
                    if (window.memberGrowthChart) window.memberGrowthChart.render();
                    if (window.paymentTrendsChart) window.paymentTrendsChart.render();
                    if (window.deathRecordsChart) window.deathRecordsChart.render();
                    if (window.ageDistributionChart) window.ageDistributionChart.render();

                    // Reapply toolbar fix after resize
                    addToolbarStyle();
                }, 250));
            });

            // Function to add toolbar style fixes for dark mode
            function addToolbarStyle() {
                // Only apply in dark mode
                if (!document.documentElement.classList.contains('dark')) return;

                // Add a style tag if it doesn't exist
                if (!document.getElementById('apexcharts-dark-fix')) {
                    const style = document.createElement('style');
                    style.id = 'apexcharts-dark-fix';
                    style.textContent = `
                        .apexcharts-toolbar svg path,
                        .apexcharts-toolbar svg line,
                        .apexcharts-toolbar svg rect,
                        .apexcharts-toolbar svg circle,
                        .apexcharts-toolbar svg polyline {
                            stroke: #e5e7eb !important;
                        }
                        .apexcharts-menu {
                            background: #374151 !important;
                            border: 1px solid #4b5563 !important;
                        }
                        .apexcharts-menu-item {
                            color: #e5e7eb !important;
                        }
                        .apexcharts-menu-item:hover {
                            background-color: #4b5563 !important;
                        }
                    `;
                    document.head.appendChild(style);
                }

                // Direct style application to any existing elements
                setTimeout(() => {
                    const icons = document.querySelectorAll('.apexcharts-toolbar svg *');
                    icons.forEach(icon => {
                        icon.style.stroke = '#e5e7eb';
                    });

                    const menus = document.querySelectorAll('.apexcharts-menu');
                    menus.forEach(menu => {
                        menu.style.background = '#374151';
                        menu.style.border = '1px solid #4b5563';
                    });

                    const menuItems = document.querySelectorAll('.apexcharts-menu-item');
                    menuItems.forEach(item => {
                        item.style.color = '#e5e7eb';
                    });
                }, 200);
            }

            // Set up a mutation observer to watch for theme changes
            function setupThemeChangeListener() {
                // Watch for theme changes
                const observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                            // Reinitialize charts when theme changes
                            initializeCharts();

                            // Fix toolbar colors immediately after theme changes
                            setTimeout(() => {
                                if (document.documentElement.classList.contains('dark')) {
                                    // Fix for dark mode
                                    const toolbarIcons = document.querySelectorAll('.apexcharts-toolbar svg, .apexcharts-toolbar line');
                                    toolbarIcons.forEach(icon => {
                                        icon.style.stroke = '#e5e7eb';
                                    });
                                } else {
                                    // Reset for light mode
                                    const toolbarIcons = document.querySelectorAll('.apexcharts-toolbar svg, .apexcharts-toolbar line');
                                    toolbarIcons.forEach(icon => {
                                        icon.style.stroke = '';
                                    });
                                }
                            }, 300);
                        }
                    });
                });

                // Start observing document for theme class changes
                observer.observe(document.documentElement, {
                    attributes: true,
                    attributeFilter: ['class']
                });
            }

            // Show error messages when charts can't be rendered
            function showChartErrors() {
                const chartContainers = ['#memberGrowthChart', '#paymentTrendsChart', '#deathRecordsChart',
                    '#ageDistributionChart'
                ];

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

            // Create optimized chart configurations with dark mode fix for toolbar
            function getChartConfigs() {
                // Detect if dark mode is active
                const isDarkMode = document.documentElement.classList.contains('dark');

                // Set appropriate colors based on theme
                const textColor = isDarkMode ? '#e5e7eb' : '#374151'; // Light gray for dark mode, dark gray for light
                const borderColor = isDarkMode ? '#374151' : '#e0e0e0';
                const dataLabelColor = isDarkMode ? '#e5e7eb' : '#304758';

                // Common chart configuration
                const commonConfig = {
                    fontFamily: 'inherit',
                    toolbar: {
                        show: true,
                        tools: {
                            download: true,
                            selection: true,
                            zoom: true,
                            reset: true
                        },
                        export: {
                            csv: {
                                filename: 'chart-data',
                                headerCategory: 'category',
                                headerValue: 'value'
                            },
                            svg: {
                                filename: 'chart',
                            },
                            png: {
                                filename: 'chart',
                            }
                        }
                    },
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800,
                        animateGradually: {
                            enabled: true,
                            delay: 150
                        }
                    },
                    theme: {
                        mode: isDarkMode ? 'dark' : 'light',
                    },
                    foreColor: textColor // This sets the base text color for the chart
                };

                // Apply toolbar color fix - direct CSS override for dark mode
                if (isDarkMode) {
                    // This will be added to the document head when charts are initialized
                    const styleElement = document.createElement('style');
                    styleElement.id = 'apexcharts-toolbar-fix';
                    styleElement.textContent = `
                        .apexcharts-menu-icon svg,
                        .apexcharts-zoom-icon svg,
                        .apexcharts-zoomin-icon svg,
                        .apexcharts-zoomout-icon svg,
                        .apexcharts-reset-icon svg,
                        .apexcharts-pan-icon svg,
                        .apexcharts-selection-icon svg,
                        .apexcharts-menu-icon line,
                        .apexcharts-toolbar-custom-icon svg {
                            stroke: #e5e7eb !important;
                        }
                        .apexcharts-menu {
                            background: #374151 !important;
                            border: 1px solid #4b5563 !important;
                        }
                        .apexcharts-menu-item {
                            color: #e5e7eb !important;
                        }
                        .apexcharts-menu-item:hover {
                            background-color: #4b5563 !important;
                        }
                    `;

                    // Remove existing style if it exists
                    const existingStyle = document.getElementById('apexcharts-toolbar-fix');
                    if (existingStyle) {
                        existingStyle.remove();
                    }

                    document.head.appendChild(styleElement);
                } else {
                    // Remove the style if not in dark mode
                    const existingStyle = document.getElementById('apexcharts-toolbar-fix');
                    if (existingStyle) {
                        existingStyle.remove();
                    }
                }

                // More granular responsive breakpoints for better adaptability
                const responsive = [
                    {
                        breakpoint: 1280,
                        options: {
                            chart: {
                                height: '100%'
                            }
                        }
                    },
                    {
                        breakpoint: 768,
                        options: {
                            chart: {
                                height: '100%'
                            },
                            legend: {
                                position: 'bottom',
                                horizontalAlign: 'center'
                            },
                            dataLabels: {
                                offsetY: -10,
                                style: {
                                    fontSize: '10px'
                                }
                            }
                        }
                    },
                    {
                        breakpoint: 480,
                        options: {
                            chart: {
                                height: '100%'
                            },
                            legend: {
                                position: 'bottom',
                                horizontalAlign: 'center',
                                fontSize: '11px'
                            },
                            xaxis: {
                                labels: {
                                    rotate: -90,
                                    style: {
                                        fontSize: '10px'
                                    }
                                }
                            },
                            yaxis: {
                                labels: {
                                    style: {
                                        fontSize: '10px'
                                    }
                                }
                            }
                        }
                    }
                ];

                return {
                    memberGrowth: {
                        chart: {
                            ...commonConfig,
                            type: 'area',
                            height: '100%'
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
                                opacityTo: 0.9,
                                stops: [0, 90, 100]
                            }
                        },
                        colors: ['#3b82f6'],
                        dataLabels: {
                            enabled: false
                        },
                        tooltip: {
                            y: {
                                formatter: function(val) {
                                    return val + ' ahli';
                                }
                            },
                            theme: isDarkMode ? 'dark' : 'light'
                        },
                        grid: {
                            borderColor: borderColor,
                            strokeDashArray: 5
                        },
                        markers: {
                            size: 4,
                            colors: ['#3b82f6'],
                            strokeColors: isDarkMode ? '#1e293b' : '#fff',
                            strokeWidth: 2,
                            hover: {
                                size: 6
                            }
                        },
                        xaxis: {
                            labels: {
                                style: {
                                    colors: textColor
                                }
                            }
                        },
                        yaxis: {
                            title: {
                                text: 'Jumlah Ahli',
                                style: {
                                    color: textColor
                                }
                            },
                            labels: {
                                style: {
                                    colors: textColor
                                }
                            }
                        },
                        legend: {
                            labels: {
                                colors: textColor
                            }
                        },
                        responsive: responsive
                    },

                    paymentTrends: {
                        chart: {
                            ...commonConfig,
                            type: 'bar',
                            height: '100%'
                        },
                        plotOptions: {
                            bar: {
                                borderRadius: 4,
                                columnWidth: '70%',
                                dataLabels: {
                                    position: 'top'
                                }
                            }
                        },
                        colors: ['#10b981'],
                        dataLabels: {
                            enabled: true,
                            formatter: function(val) {
                                return 'RM ' + val.toFixed(0);
                            },
                            offsetY: -20,
                            style: {
                                fontSize: '12px',
                                colors: [dataLabelColor]
                            }
                        },
                        tooltip: {
                            y: {
                                formatter: function(val) {
                                    return 'RM ' + val.toFixed(2);
                                }
                            },
                            theme: isDarkMode ? 'dark' : 'light'
                        },
                        grid: {
                            borderColor: borderColor,
                            strokeDashArray: 5
                        },
                        xaxis: {
                            labels: {
                                style: {
                                    colors: textColor
                                }
                            }
                        },
                        yaxis: {
                            title: {
                                text: 'Jumlah (RM)',
                                style: {
                                    color: textColor
                                }
                            },
                            labels: {
                                style: {
                                    colors: textColor
                                }
                            }
                        },
                        responsive: responsive
                    },

                    deathRecords: {
                        chart: {
                            ...commonConfig,
                            type: 'bar',
                            height: '100%',
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
                        legend: {
                            position: 'top',
                            horizontalAlign: 'left',
                            labels: {
                                colors: textColor
                            }
                        },
                        fill: {
                            opacity: 1
                        },
                        tooltip: {
                            theme: isDarkMode ? 'dark' : 'light'
                        },
                        grid: {
                            borderColor: borderColor,
                            strokeDashArray: 5
                        },
                        xaxis: {
                            labels: {
                                style: {
                                    colors: textColor
                                }
                            }
                        },
                        yaxis: {
                            title: {
                                text: 'Jumlah',
                                style: {
                                    color: textColor
                                }
                            },
                            labels: {
                                style: {
                                    colors: textColor
                                }
                            }
                        },
                        responsive: responsive
                    },

                    ageDistribution: {
                        chart: {
                            ...commonConfig,
                            type: 'donut',
                            height: '100%'
                        },
                        colors: ['#0ea5e9', '#14b8a6', '#f59e0b', '#8b5cf6', '#ef4444'],
                        legend: {
                            position: 'bottom',
                            horizontalAlign: 'center',
                            labels: {
                                colors: textColor
                            }
                        },
                        dataLabels: {
                            enabled: true,
                            formatter: function(val, opt) {
                                return opt.w.globals.series[opt.seriesIndex] + ' ahli';
                            },
                            style: {
                                colors: [dataLabelColor]
                            }
                        },
                        tooltip: {
                            y: {
                                formatter: function(val) {
                                    return val + ' ahli';
                                }
                            },
                            theme: isDarkMode ? 'dark' : 'light'
                        },
                        plotOptions: {
                            pie: {
                                donut: {
                                    size: '50%',
                                    labels: {
                                        show: true,
                                        name: {
                                            show: true,
                                            color: textColor
                                        },
                                        value: {
                                            show: true,
                                            color: textColor,
                                            formatter: function(val) {
                                                return val + ' ahli';
                                            }
                                        },
                                        total: {
                                            show: true,
                                            label: 'Jumlah',
                                            color: textColor,
                                            formatter: function(w) {
                                                return w.globals.seriesTotals.reduce((a, b) => a + b, 0) + ' ahli';
                                            }
                                        }
                                    }
                                }
                            }
                        },
                        responsive: responsive
                    }
                };
            }

            function initializeCharts() {
                try {
                    // Make sure ApexCharts is available
                    if (typeof ApexCharts === 'undefined') {
                        console.error('ApexCharts library not loaded');
                        showChartErrors();
                        return;
                    }

                    // Clear existing charts to avoid duplicates
                    const chartContainers = ['#memberGrowthChart', '#paymentTrendsChart', '#deathRecordsChart',
                        '#ageDistributionChart'
                    ];
                    chartContainers.forEach(container => {
                        document.querySelector(container).innerHTML = '';
                    });

                    // Get chart configurations
                    const configs = getChartConfigs();

                    // Add custom CSS for toolbar after ApexCharts is initialized
                    // This ensures the styles are applied to any ApexCharts instance
                    if (document.documentElement.classList.contains('dark')) {
                        // Add dark mode specific CSS for toolbar
                        setTimeout(() => {
                            const toolbarIcons = document.querySelectorAll('.apexcharts-toolbar svg, .apexcharts-toolbar line');
                            toolbarIcons.forEach(icon => {
                                icon.style.stroke = '#e5e7eb';
                            });

                            // Fix menu as well when created
                            const menuItems = document.querySelectorAll('.apexcharts-menu');
                            menuItems.forEach(menu => {
                                menu.style.background = '#374151';
                                menu.style.border = '1px solid #4b5563';
                                const items = menu.querySelectorAll('.apexcharts-menu-item');
                                items.forEach(item => {
                                    item.style.color = '#e5e7eb';
                                });
                            });
                        }, 300);
                    }

                    // Initialize charts if data is available
                    initializeMemberGrowthChart(configs.memberGrowth);
                    initializePaymentTrendsChart(configs.paymentTrends);
                    initializeDeathRecordsChart(configs.deathRecords);
                    initializeAgeDistributionChart(configs.ageDistribution);

                } catch (error) {
                    console.error('Error initializing charts:', error);
                    showChartErrors();
                }
            }

            // Debounce function to limit how often a function is called
            function debounce(func, wait) {
                let timeout;
                return function() {
                    const context = this,
                        args = arguments;
                    clearTimeout(timeout);
                    timeout = setTimeout(function() {
                        func.apply(context, args);
                    }, wait);
                };
            }

            function initializeMemberGrowthChart(configTemplate) {
                const memberData = @json($memberGrowthData);

                if (!memberData || memberData.length === 0) {
                    document.querySelector('#memberGrowthChart').innerHTML = `
                        <div class="flex items-center justify-center h-full text-gray-500">
                            <p>Tiada data untuk dipaparkan</p>
                        </div>
                    `;
                    return;
                }

                const config = {
                    ...configTemplate,
                    series: [{
                        name: 'Ahli Baru',
                        data: memberData.map(item => item.value)
                    }],
                    xaxis: {
                        categories: memberData.map(item => item.label),
                        labels: {
                            rotate: -45,
                            style: {
                                fontSize: '12px'
                            }
                        }
                    },
                    yaxis: {
                        title: {
                            text: 'Jumlah Ahli'
                        },
                        labels: {
                            formatter: function(val) {
                                return val.toFixed(0);
                            }
                        }
                    }
                };

                window.memberGrowthChart = new ApexCharts(document.querySelector("#memberGrowthChart"), config);
                window.memberGrowthChart.render();
            }

            function initializePaymentTrendsChart(configTemplate) {
                const paymentData = @json($paymentTrendsData);

                if (!paymentData || paymentData.length === 0) {
                    document.querySelector('#paymentTrendsChart').innerHTML = `
                        <div class="flex items-center justify-center h-full text-gray-500">
                            <p>Tiada data untuk dipaparkan</p>
                        </div>
                    `;
                    return;
                }

                const config = {
                    ...configTemplate,
                    series: [{
                        name: 'Jumlah Pembayaran',
                        data: paymentData.map(item => item.value)
                    }],
                    xaxis: {
                        categories: paymentData.map(item => item.label),
                        labels: {
                            rotate: -45,
                            style: {
                                fontSize: '12px'
                            }
                        }
                    },
                    yaxis: {
                        title: {
                            text: 'Jumlah (RM)'
                        },
                        labels: {
                            formatter: function(val) {
                                return 'RM ' + val.toFixed(2);
                            }
                        }
                    }
                };

                window.paymentTrendsChart = new ApexCharts(document.querySelector("#paymentTrendsChart"), config);
                window.paymentTrendsChart.render();
            }

            function initializeDeathRecordsChart(configTemplate) {
                const deathData = @json($deathRecordsData);

                if (!deathData || !deathData.labels || deathData.labels.length === 0) {
                    document.querySelector('#deathRecordsChart').innerHTML = `
                        <div class="flex items-center justify-center h-full text-gray-500">
                            <p>Tiada data untuk dipaparkan</p>
                        </div>
                    `;
                    return;
                }

                const config = {
                    ...configTemplate,
                    series: [{
                            name: 'Ahli',
                            data: deathData.memberDeaths
                        },
                        {
                            name: 'Tanggungan',
                            data: deathData.dependentDeaths
                        }
                    ],
                    xaxis: {
                        categories: deathData.labels,
                        labels: {
                            rotate: -45,
                            style: {
                                fontSize: '11px'
                            }
                        }
                    },
                    yaxis: {
                        title: {
                            text: 'Jumlah'
                        },
                        labels: {
                            formatter: function(val) {
                                return val.toFixed(0);
                            }
                        }
                    },
                    // Make the chart more compact
                    chart: {
                        ...configTemplate.chart,
                        height: '100%'
                    },
                    // Reduce padding/margin
                    plotOptions: {
                        ...configTemplate.plotOptions,
                        bar: {
                            ...configTemplate.plotOptions?.bar,
                            columnWidth: '60%'
                        }
                    },
                    // Make legend more compact
                    legend: {
                        ...configTemplate.legend,
                        fontSize: '11px',
                        itemMargin: {
                            horizontal: 5,
                            vertical: 0
                        }
                    }
                };

                window.deathRecordsChart = new ApexCharts(document.querySelector("#deathRecordsChart"), config);
                window.deathRecordsChart.render();
            }

            function initializeAgeDistributionChart(configTemplate) {
                const ageData = @json($ageDistributionData);

                if (!ageData || ageData.length === 0) {
                    document.querySelector('#ageDistributionChart').innerHTML = `
                        <div class="flex items-center justify-center h-full text-gray-500">
                            <p>Tiada data untuk dipaparkan</p>
                        </div>
                    `;
                    return;
                }

                const config = {
                    ...configTemplate,
                    series: ageData.map(item => item.count),
                    labels: ageData.map(item => item.range)
                };

                window.ageDistributionChart = new ApexCharts(document.querySelector("#ageDistributionChart"), config);
                window.ageDistributionChart.render();
            }

            // Listen for Livewire updates
            document.addEventListener('livewire:initialized', function() {
                Livewire.on('forceRefresh', () => {
                    window.location.reload();
                });
            });
        </script>

</x-filament-panels::page>
