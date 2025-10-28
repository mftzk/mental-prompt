<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prompt Quality Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        
        /* --------------------------------------------------
           Global Color Variables – refined, elegant palette
        -------------------------------------------------- */
        :root {
            --clr-primary:      #3F7D58; /* primary-green */
            --clr-accent:       #EF9651; /* primary-orange */
            --clr-danger:       #EC5228; /* primary-red-orange */
            --clr-bg:           #ffffff; /* primary-white */
            --clr-card-bg:      #ffffff;
            --clr-border:       #d1d5db;  /* gray-300 */
            --clr-text-main:    #1f2937;  /* gray-800 */
            --clr-text-sub:     #4b5563;  /* gray-600 */
            --clr-text-on-primary: #ffffff;
            --clr-shadow:       rgba(0, 0, 0, 0.08);
            --clr-shadow-lg:    rgba(0, 0, 0, 0.12);
        }

        /* --------------------------------------------------
           Dark Mode Color Variables
        -------------------------------------------------- */
        :root[data-theme="dark"] {
            --clr-primary:      #3F7D58; /* keep green for charts */
            --clr-accent:       #FF6B35; /* bright orange accent */
            --clr-danger:       #EC5228; /* primary-red-orange */
            --clr-bg:           #1a1f2e; /* dark navy background */
            --clr-card-bg:      #242b3d; /* elevated card surface */
            --clr-border:       #2d3748;  /* subtle borders */
            --clr-text-main:    #e5e7eb;  /* light gray text */
            --clr-text-sub:     #9ca3af;  /* muted gray text */
            --clr-text-on-primary: #ffffff;
            --clr-shadow:       rgba(0, 0, 0, 0.3);
            --clr-shadow-lg:    rgba(0, 0, 0, 0.5);
        }
        
        .gradient-bg {
            background-color: var(--clr-bg);
            min-height: 100vh;
        }
        
        .glass-card {
            background: var(--clr-card-bg);
            backdrop-filter: none;
            border: 1px solid var(--clr-border);
            border-radius: 12px;
            box-shadow: none;
            transition: all 0.3s ease;
        }
        
        .glass-card:hover {
            box-shadow: none;
            transform: none;
        }
        
        .range-btn {
            border: 1px solid var(--clr-border);
            border-radius: 12px;
            transition: all 0.2s ease;
            padding: 12px 20px;
            font-weight: 500;
            background-color: var(--clr-card-bg);
            color: var(--clr-text-main);
        }
        
        .range-btn:hover {
            background-color: rgba(63, 125, 88, 0.1);
            border-color: var(--clr-primary);
        }
        
        .range-btn.active {
            background: var(--clr-primary);
            border: 1px solid var(--clr-primary);
            color: var(--clr-text-on-primary) !important;
            box-shadow: 0 4px 15px rgba(63, 125, 88, 0.35);
        }
        
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
            padding: 0;
        }
        
        .header-gradient {
            background: var(--clr-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .card-hover:hover {
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.04);
        }
        
        body {
            color: var(--clr-text-main);
        }

        /* --------------------------------------------------
           Enhanced component styles
        -------------------------------------------------- */
        button:not(.range-btn) {
            background: var(--clr-primary);
            color: var(--clr-text-on-primary);
            border: none;
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        button:not(.range-btn):hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        select {
            border: 1px solid var(--clr-border);
            background-color: var(--clr-card-bg);
            color: var(--clr-text-main);
            padding: 12px 16px;
            border-radius: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            min-width: 200px;
        }

        select:focus {
            outline: none;
            border-color: var(--clr-primary);
            box-shadow: 0 0 0 3px rgba(63, 125, 88, 0.1);
        }

        .control-section {
            padding: 32px 28px;
            border-radius: 20px;
        }

        .header-section {
            padding: 40px 0 60px 0;
        }

        .chart-section {
            padding: 12px;
        }

        .control-group {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 24px;
        }

        .control-label {
            font-weight: 600;
            font-size: 14px;
            color: var(--clr-text-main);
            margin-bottom: 8px;
        }

        .button-group {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        @media (max-width: 768px) {
            .control-section {
                padding: 20px;
                flex-direction: column;
                gap: 24px;
            }
            
            .chart-section {
                padding: 24px;
            }
            
            .button-group {
                justify-content: center;
            }
        }

        /* ----------------------------------------------
           Disable container/element transition animations
        ---------------------------------------------- */
        .glass-card, .glass-card:hover,
        .range-btn,
        button:not(.range-btn),
        select {
            transition: none !important;
            transform: none !important;
        }

        /* Reusable styled input (matches select) */
        .input-styled {
            border: 1px solid var(--clr-border);
            background-color: var(--clr-card-bg);
            color: var(--clr-text-main);
            padding: 12px 16px;
            border-radius: 12px;
            font-weight: 500;
            min-width: 240px;
        }

        .input-styled:focus {
            outline: none;
            border-color: var(--clr-primary);
            box-shadow: 0 0 0 3px rgba(63, 125, 88, 0.1);
        }

        /* remove bottom margin for last control group inside wrapper */
        .flex.items-center .control-group:last-child {
            margin-bottom: 0;
        }

        /* Chart Container Layout */
        .charts-container {
            display: flex;
            flex-direction: row;
            gap: 16px;
            align-items: stretch;
            margin-top: 16px;
        }

        .chart-wrapper {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
        }

        /* Responsive: Stack charts vertically on smaller screens */
        @media (max-width: 1024px) {
            .charts-container {
                flex-direction: column;
                gap: 24px;
            }

            .chart-wrapper {
                width: 100%;
            }
        }

        /* Theme Toggle Button Styles */
        .theme-toggle {
            position: fixed;
            top: 24px;
            right: 24px;
            background: var(--clr-card-bg);
            border: 1px solid var(--clr-border);
            border-radius: 12px;
            padding: 12px;
            cursor: pointer;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            box-shadow: 0 4px 12px var(--clr-shadow);
        }

        .theme-toggle:hover {
            background: var(--clr-accent);
            border-color: var(--clr-accent);
        }

        .theme-toggle svg {
            width: 24px;
            height: 24px;
            fill: var(--clr-text-main);
        }

        .theme-toggle:hover svg {
            fill: var(--clr-text-on-primary);
        }

        .theme-toggle .sun-icon {
            display: none;
        }

        .theme-toggle .moon-icon {
            display: block;
        }

        :root[data-theme="dark"] .theme-toggle .sun-icon {
            display: block;
        }

        :root[data-theme="dark"] .theme-toggle .moon-icon {
            display: none;
        }

    </style>
</head>
<body class="gradient-bg">
    <!-- Theme Toggle Button -->
    <button class="theme-toggle" onclick="toggleTheme()" aria-label="Toggle theme">
        <svg class="sun-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="5"/>
            <line x1="12" y1="1" x2="12" y2="3"/>
            <line x1="12" y1="21" x2="12" y2="23"/>
            <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/>
            <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
            <line x1="1" y1="12" x2="3" y2="12"/>
            <line x1="21" y1="12" x2="23" y2="12"/>
            <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/>
            <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
        </svg>
        <svg class="moon-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
        </svg>
    </button>

    <div class="max-w-7xl mx-auto px-6 py-8">
        <!-- Header + Filters Container -->
        <div x-data="dashboardControls()" class="mb-12 flex justify-center">
            <div class="glass-card control-section flex flex-col gap-10">
                <!-- Title & Subtitle -->
                <div>
                    <h1 class="header-gradient font-extrabold leading-tight text-4xl sm:text-5xl lg:text-6xl">
                        Prompt Quality Analytics
                    </h1>
                    <p class="mt-4 text-base sm:text-lg lg:text-xl font-normal max-w-2xl" style="color: var(--clr-text-sub)">
                        Real&#8209;time insights into prompt effectiveness across client sessions
                    </p>
                </div>
                <!-- Filter Controls Wrapper -->
                <div class="flex items-center gap-8 flex-wrap lg:flex-nowrap">
                    <!-- Range Picker -->
                    <div class="control-group">
                        <div class="control-label">Time Range</div>
                        <div class="button-group">
                            <template x-for="opt in options" :key="opt.value">
                                <button @click="setRange(opt.value)"
                                    class="rounded-xl font-medium range-btn"
                                    :class="range===opt.value ? 'active' : ''"
                                    x-text="opt.label"
                                    :style="range !== opt.value ? { color: 'var(--clr-text-main)' } : {}"></button>
                            </template>
                        </div>
                    </div>
                    
                    <!-- Project Picker -->
                    <div class="control-group">
                        <div class="control-label">Project</div>
                        <select x-model="project" @change="updateProject()">
                            <option value="">All Projects</option>
                            <template x-for="opt in projectOptions" :key="opt">
                                <option :value="opt" x-text="opt"></option>
                            </template>
                        </select>
                    </div>
                    
                </div> <!-- end filter controls wrapper -->
            </div> <!-- end glass-card -->
        </div> <!-- end x-data container -->

        <script>
            function dashboardControls(){
                const current = '{{ $range }}';
                const currentProject = '{{ $selectedProject }}';
                return {
                    range: current,
                    project: currentProject,
                    projectOptions: JSON.parse(document.getElementById('projectsData').textContent),
                    options: [
                        {value:'30m',label:'30 Min'},
                        {value:'1h',label:'1 Hour'},
                        {value:'1d',label:'1 Day'},
                        {value:'1w',label:'1 Week'},
                        {value:'1m',label:'1 Month'},
                        {value:'all',label:'All Time'},
                    ],
                    setRange(r){
                        if(r!==this.range){
                            const url = new URL(window.location.href);
                            url.searchParams.set('range', r);
                            if(this.project) url.searchParams.set('project', this.project);
                            window.location = url;
                        }
                    },
                    updateProject(){
                        const url = new URL(window.location.href);
                        if(this.project){
                            url.searchParams.set('project', this.project);
                        }else{
                            url.searchParams.delete('project');
                        }
                        if(this.range) url.searchParams.set('range', this.range);
                        window.location = url;
                    },
                }
            }
        </script>

        <script type="application/json" id="timeSeriesData">{!! json_encode($timeSeriesStats) !!}</script>
        <script type="application/json" id="overallData">{!! json_encode($overallStats) !!}</script>
        <script type="application/json" id="projectsData">{!! $availableProjects->toJson() !!}</script>
        <script type="application/json" id="granularData">{!! $granularStats->toJson() !!}</script>
        <script type="application/json" id="showGranularChart">{{ $showGranularChart ? 'true' : 'false' }}</script>

        <!-- Charts Container -->
        <div class="charts-container">

        <!-- ===== OVERALL STATS SECTION ===== -->
        <div class="chart-wrapper">
            <div class="glass-card chart-section">
                <div class="flex items-start justify-between mb-8 flex-wrap gap-6">
                    <div class="flex-1 min-w-0">
                        <h2 class="text-3xl font-bold text-[var(--clr-text-main)] mb-3">
                            Overall Project Performance
                        </h2>
                        <p class="text-lg text-[var(--clr-text-sub)] font-normal leading-relaxed">
                            Complete historical overview of prompt quality across all time periods.
                        </p>
                        <div class="mt-4 flex gap-6 text-sm">
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-[var(--clr-text-main)]">Total Records:</span>
                                <span class="text-[var(--clr-text-sub)]" x-data="{ data: {!! json_encode($overallStats) !!} }" x-text="data?.total_records || 0"></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-[var(--clr-text-main)]">Projects:</span>
                                <span class="text-[var(--clr-text-sub)]" x-data="{ data: {!! json_encode($overallStats) !!} }" x-text="data?.total_projects || 0"></span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3 bg-gray-50 px-4 py-3 rounded-xl">
                        <div class="w-5 h-5 rounded-full" style="background-color: var(--clr-primary)"></div>
                        <span class="text-sm text-[var(--clr-text-sub)] font-semibold">Avg Efektivitas</span>
                    </div>
                    <div class="flex items-center space-x-3 bg-gray-50 px-4 py-3 rounded-xl">
                        <div class="w-5 h-5 rounded-full" style="background-color: var(--clr-accent)"></div>
                        <span class="text-sm text-[var(--clr-text-sub)] font-semibold">Avg Membingungkan</span>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="overallChart"></canvas>
                </div>
            </div>
        </div>

        <!-- ===== TIME SERIES SECTION (Show when range is 'all') ===== -->
        @if($range === 'all')
        <div class="glass-card chart-wrapper">
            <div class="chart-section">
            <div class="flex items-start justify-between mb-8 flex-wrap gap-6">
                <div class="flex-1 min-w-0">
                    <h2 class="text-3xl font-bold text-[var(--clr-text-main)] mb-3">
                        Time Series Analysis
                    </h2>
                    <p class="text-lg text-[var(--clr-text-sub)] font-normal leading-relaxed">
                        Analyze prompt quality trends and patterns within selected time periods.
                    </p>
                    <div class="mt-4 flex gap-6 text-sm">
                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-[var(--clr-text-main)]">Period:</span>
                            <span class="text-[var(--clr-text-sub)]" x-data="getCurrentRangeLabel()" x-text="$el.textContent"></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-[var(--clr-text-main)]">Records:</span>
                            <span class="text-[var(--clr-text-sub)]" x-data="{ data: {!! json_encode($timeSeriesStats) !!} }" x-text="data?.total_records || 0"></span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-3 bg-gray-50 px-4 py-3 rounded-xl">
                    <div class="w-5 h-5 rounded-full" style="background-color: var(--clr-primary)"></div>
                    <span class="text-sm text-[var(--clr-text-sub)] font-semibold">Avg Efektivitas</span>
                </div>
                <div class="flex items-center space-x-3 bg-gray-50 px-4 py-3 rounded-xl">
                    <div class="w-5 h-5 rounded-full" style="background-color: var(--clr-accent)"></div>
                    <span class="text-sm text-[var(--clr-text-sub)] font-semibold">Avg Membingungkan</span>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="timeSeriesChart"></canvas>
            </div>
            </div>
        </div>
        @endif

        <!-- ===== GRANULAR TIME-SERIES SECTION (Show when range is not 'all') ===== -->
        @if($showGranularChart)
        <div class="glass-card chart-wrapper">
            <div class="chart-section">
            <div class="flex items-start justify-between mb-8 flex-wrap gap-6">
                <div class="flex-1 min-w-0">
                    <h2 class="text-3xl font-bold text-[var(--clr-text-main)] mb-3">
                        Granular Time-Series Analysis
                    </h2>
                    <p class="text-lg text-[var(--clr-text-sub)] font-normal leading-relaxed">
                        Detailed prompt quality trends with 
                        @if($range === '30m') 10-second
                        @elseif($range === '1h') 30-second
                        @elseif($range === '1d') 30-second
                        @elseif($range === '1w') hourly
                        @elseif($range === '1m') daily
                        @endif
                        interval aggregation.
                    </p>
                    <div class="mt-4 flex gap-6 text-sm">
                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-[var(--clr-text-main)]">Period:</span>
                            <span class="text-[var(--clr-text-sub)]" x-data="getCurrentRangeLabel()" x-text="$el.textContent"></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-[var(--clr-text-main)]">Data Points:</span>
                            <span class="text-[var(--clr-text-sub)]" x-data="{ data: {!! $granularStats->toJson() !!} }" x-text="data?.length || 0"></span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-3 bg-gray-50 px-4 py-3 rounded-xl">
                    <div class="w-5 h-5 rounded-full" style="background-color: var(--clr-primary)"></div>
                    <span class="text-sm text-[var(--clr-text-sub)] font-semibold">Avg Efektivitas</span>
                </div>
                <div class="flex items-center space-x-3 bg-gray-50 px-4 py-3 rounded-xl">
                    <div class="w-5 h-5 rounded-full" style="background-color: var(--clr-accent)"></div>
                    <span class="text-sm text-[var(--clr-text-sub)] font-semibold">Avg Membingungkan</span>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="granularChart"></canvas>
            </div>
            </div>
        </div>
        @endif

        </div>
    </div> <!-- end glass-card -->
</div> <!-- end x-data container -->

<script>
    // ===== THEME MANAGEMENT =====
    // Initialize theme on page load (default to dark)
    (function initTheme() {
        const savedTheme = localStorage.getItem('theme') || 'dark';
        document.documentElement.setAttribute('data-theme', savedTheme);
    })();

    // Toggle theme function
    function toggleTheme() {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        
        // Update charts with new theme colors
        updateChartColors(newTheme);
    }

    // Update chart colors based on theme
    function updateChartColors(theme) {
        const isDark = theme === 'dark';
        const textColor = isDark ? '#9ca3af' : '#64748b';
        const borderColor = isDark ? '#2d3748' : '#e2e8f0';
        const gridColor = isDark ? 'rgba(45, 55, 72, 0.3)' : 'rgba(200,200,200,0.08)';
        
        // Update all charts if they exist
        if (window.overallChart) {
            window.overallChart.options.scales.y.ticks.color = textColor;
            window.overallChart.options.scales.y.grid.borderColor = borderColor;
            window.overallChart.options.scales.y.grid.color = gridColor;
            window.overallChart.options.scales.x.ticks.color = textColor;
            window.overallChart.update();
        }
        
        if (window.timeSeriesChart) {
            window.timeSeriesChart.options.scales.y.ticks.color = textColor;
            window.timeSeriesChart.options.scales.y.grid.borderColor = borderColor;
            window.timeSeriesChart.options.scales.y.grid.color = gridColor;
            window.timeSeriesChart.options.scales.x.ticks.color = textColor;
            window.timeSeriesChart.update();
        }
        
        if (window.granularChart) {
            window.granularChart.options.scales.y.ticks.color = textColor;
            window.granularChart.options.scales.y.grid.borderColor = borderColor;
            window.granularChart.options.scales.y.grid.color = gridColor;
            window.granularChart.options.scales.x.ticks.color = textColor;
            window.granularChart.update();
        }
    }

    // ===== CHART DATA SETUP =====
    // Retrieve datasets embedded as JSON
    const timeSeriesData = JSON.parse(document.getElementById('timeSeriesData').textContent);
    const overallData = JSON.parse(document.getElementById('overallData').textContent);

    const timeSeriesAvgQuality = parseFloat(timeSeriesData?.avg_quality) || 0;
    const timeSeriesAvgConfusion = parseFloat(timeSeriesData?.avg_confusion) || 0;

    const overallAvgQuality = parseFloat(overallData?.avg_quality) || 0;
    const overallAvgConfusion = parseFloat(overallData?.avg_confusion) || 0;

    const primaryColor = getComputedStyle(document.documentElement).getPropertyValue('--clr-primary').trim() || '#3F7D58';
    const accentColor = getComputedStyle(document.documentElement).getPropertyValue('--clr-accent').trim() || '#EF9651';

    // Helper function to get current theme colors
    function getThemeColors() {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const isDark = currentTheme === 'dark';
        return {
            textColor: isDark ? '#9ca3af' : '#64748b',
            textMain: isDark ? '#e5e7eb' : '#1f2937',
            textSub: isDark ? '#9ca3af' : '#4b5563',
            borderColor: isDark ? '#2d3748' : '#e2e8f0',
            gridColor: isDark ? 'rgba(45, 55, 72, 0.3)' : 'rgba(200,200,200,0.08)'
        };
    }

    const colors = getThemeColors();

    // ===== OVERALL CHART =====
    const ctxOverall = document.getElementById('overallChart').getContext('2d');
    window.overallChart = new Chart(ctxOverall, {
        type: 'bar',
        data: {
            labels: ['Overall Performance'],
            datasets: [
                {
                    label: 'Avg Efektivitas',
                    data: [overallAvgQuality],
                    backgroundColor: 'rgba(63, 125, 88, 0.7)',
                    borderColor: primaryColor,
                    borderWidth: 2,
                    borderRadius: 8,
                },
                {
                    label: 'Avg Membingungkan',
                    data: [overallAvgConfusion],
                    backgroundColor: 'rgba(239, 150, 81, 0.7)',
                    borderColor: accentColor,
                    borderWidth: 2,
                    borderRadius: 8,
                }
            ]
        },
        options: {
            animation: false,
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        boxWidth: 12,
                    }
                },
                tooltip: {
                    backgroundColor: colors.textColor === '#9ca3af' ? 'rgba(36, 43, 61, 0.95)' : 'rgba(255, 255, 255, 0.95)',
                    titleColor: colors.textMain,
                    bodyColor: colors.textSub,
                    borderColor: colors.borderColor,
                    borderWidth: 1,
                    cornerRadius: 12,
                    padding: 12,
                    titleFont: { weight: '600' },
                    bodyFont: { weight: '500' }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        stepSize: 10,
                        color: colors.textColor,
                        font: { weight: 500 },
                        padding: 12
                    },
                    grid: {
                        borderColor: colors.borderColor,
                        color: colors.gridColor
                    },
                },
                x: {
                    grid: { display: false },
                    ticks: {
                        color: colors.textColor,
                        font: { weight: 500 },
                        padding: 12
                    }
                }
            }
        }
    });

    // ===== TIME SERIES CHART (Only when range is 'all') =====
    const timeSeriesCanvas = document.getElementById('timeSeriesChart');
    if (timeSeriesCanvas) {
        const ctxTimeSeries = timeSeriesCanvas.getContext('2d');
        window.timeSeriesChart = new Chart(ctxTimeSeries, {
            type: 'bar',
            data: {
                labels: ['Current Period'],
                datasets: [
                    {
                        label: 'Avg Efektivitas',
                        data: [timeSeriesAvgQuality],
                        backgroundColor: 'rgba(63, 125, 88, 0.7)',
                        borderColor: primaryColor,
                        borderWidth: 2,
                        borderRadius: 8,
                    },
                    {
                        label: 'Avg Membingungkan',
                        data: [timeSeriesAvgConfusion],
                        backgroundColor: 'rgba(239, 150, 81, 0.7)',
                        borderColor: accentColor,
                        borderWidth: 2,
                        borderRadius: 8,
                    }
                ]
            },
            options: {
                animation: false,
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            boxWidth: 12,
                        }
                    },
                    tooltip: {
                        backgroundColor: colors.textColor === '#9ca3af' ? 'rgba(36, 43, 61, 0.95)' : 'rgba(255, 255, 255, 0.95)',
                        titleColor: colors.textMain,
                        bodyColor: colors.textSub,
                        borderColor: colors.borderColor,
                        borderWidth: 1,
                        cornerRadius: 12,
                        padding: 12,
                        titleFont: { weight: '600' },
                        bodyFont: { weight: '500' }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            stepSize: 10,
                            color: colors.textColor,
                            font: { weight: 500 },
                            padding: 12
                        },
                        grid: {
                            borderColor: colors.borderColor,
                            color: colors.gridColor
                        },
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: colors.textColor,
                            font: { weight: 500 },
                            padding: 12
                        }
                    }
                }
            }
        });
    }

    // ===== GRANULAR CHART (Only when range is not 'all') =====
    const granularCanvas = document.getElementById('granularChart');
    if (granularCanvas) {
        const ctxGranular = granularCanvas.getContext('2d');
        const granularData = JSON.parse(document.getElementById('granularData').textContent || '[]');
        const currentRange = '{{ $range }}';
        
        // Helper function to format timestamps based on range
        function formatTimestamp(timestamp, range) {
            const date = new Date(timestamp);
            
            switch(range) {
                case '30m':
                case '1h':
                    // Format: HH:mm:ss
                    return date.toLocaleTimeString('en-US', { 
                        hour: '2-digit', 
                        minute: '2-digit', 
                        second: '2-digit',
                        hour12: false 
                    });
                case '1d':
                    // Format: HH:mm
                    return date.toLocaleTimeString('en-US', { 
                        hour: '2-digit', 
                        minute: '2-digit',
                        hour12: false 
                    });
                case '1w':
                    // Format: DD-MMM HH:mm
                    return date.toLocaleDateString('en-US', { 
                        day: '2-digit', 
                        month: 'short' 
                    }) + ' ' + date.toLocaleTimeString('en-US', { 
                        hour: '2-digit', 
                        minute: '2-digit',
                        hour12: false 
                    });
                case '1m':
                    // Format: DD-MMM
                    return date.toLocaleDateString('en-US', { 
                        day: '2-digit', 
                        month: 'short' 
                    });
                default:
                    return timestamp;
            }
        }
        
        const granularLabels = granularData.map(item => formatTimestamp(item.timestamp, currentRange));
        const granularQualityData = granularData.map(item => parseFloat(item.avg_quality));
        const granularConfusionData = granularData.map(item => parseFloat(item.avg_confusion));
        
        // Adjust point radius based on data density
        const pointRadius = granularData.length > 100 ? 2 : (granularData.length > 50 ? 3 : 4);
        
        window.granularChart = new Chart(ctxGranular, {
            type: 'line',
            data: {
                labels: granularLabels,
                datasets: [
                    {
                        label: 'Avg Efektivitas',
                        data: granularQualityData,
                        borderColor: primaryColor,
                        backgroundColor: 'rgba(63, 125, 88, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: primaryColor,
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: pointRadius,
                        pointHoverRadius: pointRadius + 2,
                    },
                    {
                        label: 'Avg Membingungkan',
                        data: granularConfusionData,
                        borderColor: accentColor,
                        backgroundColor: 'rgba(239, 150, 81, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: accentColor,
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: pointRadius,
                        pointHoverRadius: pointRadius + 2,
                    }
                ]
            },
            options: {
                animation: false,
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            boxWidth: 12,
                        }
                    },
                    tooltip: {
                        backgroundColor: colors.textColor === '#9ca3af' ? 'rgba(36, 43, 61, 0.95)' : 'rgba(255, 255, 255, 0.95)',
                        titleColor: colors.textMain,
                        bodyColor: colors.textSub,
                        borderColor: colors.borderColor,
                        borderWidth: 1,
                        cornerRadius: 12,
                        padding: 12,
                        titleFont: { weight: '600' },
                        bodyFont: { weight: '500' },
                        callbacks: {
                            title: function(context) {
                                return context[0].label;
                            },
                            label: function(context) {
                                return `${context.dataset.label}: ${context.parsed.y.toFixed(1)}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            stepSize: 10,
                            color: colors.textColor,
                            font: { weight: 500 },
                            padding: 12
                        },
                        grid: {
                            borderColor: colors.borderColor,
                            color: colors.gridColor
                        },
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: colors.textColor,
                            font: { weight: 500 },
                            padding: 12,
                            maxTicksLimit: 20,
                            autoSkip: true
                        }
                    }
                }
            }
        });
    }

    // Helper function for current range label
    function getCurrentRangeLabel() {
        const currentRange = '{{ $range }}';
        const labels = {
            '30m': 'Last 30 Minutes',
            '1h': 'Last Hour',
            '1d': 'Last Day',
            '1w': 'Last Week',
            '1m': 'Last Month',
            'all': 'All Time'
        };
        return labels[currentRange] || 'Last Day';
    }

    // Set the range label
    document.addEventListener('DOMContentLoaded', function() {
        const rangeLabels = document.querySelectorAll('[x-data="getCurrentRangeLabel()"]');
        rangeLabels.forEach(el => {
            el.textContent = getCurrentRangeLabel();
        });
    });
</script>
</body>
</html> 