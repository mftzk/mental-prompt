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
        
        .gradient-bg {
            background-color: var(--clr-bg);
            min-height: 100vh;
        }
        
        .glass-card {
            background: transparent;
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
        }
        
        .range-btn:hover {
            background-color: rgba(63, 125, 88, 0.05);
            border-color: var(--clr-primary);
        }
        
        .range-btn.active {
            background: var(--clr-primary);
            border: 1px solid var(--clr-primary);
            color: var(--clr-text-on-primary);
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

    </style>
</head>
<body class="gradient-bg">
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
                                    :class="range===opt.value ? 'active' : 'bg-white'"
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

        <!-- ===== TIME SERIES SECTION ===== -->
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
        </div>
    </div> <!-- end glass-card -->
</div> <!-- end x-data container -->

<script>
    // Retrieve datasets embedded as JSON
    const timeSeriesData = JSON.parse(document.getElementById('timeSeriesData').textContent);
    const overallData = JSON.parse(document.getElementById('overallData').textContent);

    const timeSeriesAvgQuality = parseFloat(timeSeriesData?.avg_quality) || 0;
    const timeSeriesAvgConfusion = parseFloat(timeSeriesData?.avg_confusion) || 0;

    const overallAvgQuality = parseFloat(overallData?.avg_quality) || 0;
    const overallAvgConfusion = parseFloat(overallData?.avg_confusion) || 0;

    const primaryColor = getComputedStyle(document.documentElement).getPropertyValue('--clr-primary').trim() || '#3F7D58';
    const accentColor = getComputedStyle(document.documentElement).getPropertyValue('--clr-accent').trim() || '#EF9651';

    // ===== OVERALL CHART =====
    const ctxOverall = document.getElementById('overallChart').getContext('2d');
    new Chart(ctxOverall, {
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
                    backgroundColor: 'rgba(255, 255, 255, 0.95)',
                    titleColor: getComputedStyle(document.documentElement).getPropertyValue('--clr-text-main').trim() || '#1f2937',
                    bodyColor: getComputedStyle(document.documentElement).getPropertyValue('--clr-text-sub').trim() || '#4b5563',
                    borderColor: getComputedStyle(document.documentElement).getPropertyValue('--clr-border').trim() || '#d1d5db',
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
                        color: getComputedStyle(document.documentElement).getPropertyValue('--clr-text-sub').trim() || '#64748b',
                        font: { weight: 500 },
                        padding: 12
                    },
                    grid: {
                        borderColor: getComputedStyle(document.documentElement).getPropertyValue('--clr-border').trim() || '#e2e8f0',
                        color: 'rgba(200,200,200,0.08)'
                    },
                },
                x: {
                    grid: { display: false },
                    ticks: {
                        color: getComputedStyle(document.documentElement).getPropertyValue('--clr-text-sub').trim() || '#64748b',
                        font: { weight: 500 },
                        padding: 12
                    }
                }
            }
        }
    });

    // ===== TIME SERIES CHART =====
    const ctxTimeSeries = document.getElementById('timeSeriesChart').getContext('2d');
    new Chart(ctxTimeSeries, {
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
                    backgroundColor: 'rgba(255, 255, 255, 0.95)',
                    titleColor: getComputedStyle(document.documentElement).getPropertyValue('--clr-text-main').trim() || '#1f2937',
                    bodyColor: getComputedStyle(document.documentElement).getPropertyValue('--clr-text-sub').trim() || '#4b5563',
                    borderColor: getComputedStyle(document.documentElement).getPropertyValue('--clr-border').trim() || '#d1d5db',
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
                        color: getComputedStyle(document.documentElement).getPropertyValue('--clr-text-sub').trim() || '#64748b',
                        font: { weight: 500 },
                        padding: 12
                    },
                    grid: {
                        borderColor: getComputedStyle(document.documentElement).getPropertyValue('--clr-border').trim() || '#e2e8f0',
                        color: 'rgba(200,200,200,0.08)'
                    },
                },
                x: {
                    grid: { display: false },
                    ticks: {
                        color: getComputedStyle(document.documentElement).getPropertyValue('--clr-text-sub').trim() || '#64748b',
                        font: { weight: 500 },
                        padding: 12
                    }
                }
            }
        }
    });

    // Helper function for current range label
    function getCurrentRangeLabel() {
        const currentRange = '{{ $range }}';
        const labels = {
            '30m': 'Last 30 Minutes',
            '1h': 'Last Hour',
            '1d': 'Last Day',
            '1w': 'Last Week',
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