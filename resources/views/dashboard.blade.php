<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prompt Quality Dashboard</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('img/mntl.svg') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        
        /* --------------------------------------------------
           Global Color Variables – refined, elegant palette
        -------------------------------------------------- */
        :root {
            /* Earth Tone Palette */
            --clr-primary:      #6A994E; /* Muted Olive Green */
            --clr-primary-rgb:  106, 153, 78;
            --clr-accent:       #BC6C25; /* Terracotta Brown */
            --clr-danger:       #A44A3F; /* Earthy Red */
            --clr-ambiguity:    #D9465B; /* Unchanged, or could be a muted purple */
            --clr-bg:           #ffffff; /* primary-white */
            --clr-card-bg:      #ffffff;
            --clr-border:       #d1d5db;  /* gray-300 */
            --clr-text-main:    #1f2937;  /* gray-800 */
            --clr-text-sub:     #4b5563;  /* gray-600 */
            --clr-text-on-primary: #1a1f2e; /* Dark text on light green button */
            --clr-shadow:       rgba(0, 0, 0, 0.08);
            --clr-shadow-lg:    rgba(0, 0, 0, 0.12);
            
            /* Border Radius System - Less Rounded */
            --radius-sm: 6px;
            --radius-md: 8px;
            --radius-lg: 12px;
            --radius-full: 9999px;
        }

        /* --------------------------------------------------
           Dark Mode Color Variables
        -------------------------------------------------- */
        :root[data-theme="dark"] {
            --clr-primary:      #7CB342; /* Lighter Olive for dark mode */
            --clr-primary-rgb:  124, 179, 66;
            --clr-accent:       #DDA15E; /* Lighter Terracotta */
            --clr-danger:       #B85B52;
            --clr-bg:           #1a1f2e; /* dark navy background */
            --clr-card-bg:      #242b3d; /* elevated card surface */
            --clr-border:       #2d3748;  /* subtle borders */
            --clr-text-main:    #e5e7eb;  /* light gray text */
            --clr-text-sub:     #9ca3af;  /* muted gray text */
            --clr-text-on-primary: #1a1f2e; /* Dark text on light green button */
            --clr-shadow:       rgba(0, 0, 0, 0.3);
            --clr-shadow-lg:    rgba(0, 0, 0, 0.5);
        }
        
        .gradient-bg {
            background-color: var(--clr-bg);
            min-height: 100vh;
        }

        /* Navbar Styles */
        .navbar {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: var(--clr-bg); /* Change from --clr-card-bg */
            border-bottom: none;       /* Remove border */
            box-shadow: none;          /* Remove shadow */
            backdrop-filter: blur(10px);
        }

        .navbar-content {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 64px;
        }

        .navbar-brand {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--clr-text-main);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .navbar-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .navbar-client-info {
            display: none;
            font-size: 0.875rem;
            color: var(--clr-text-sub);
            gap: 0.75rem;
            align-items: center;
        }

        @media (min-width: 768px) {
            .navbar-client-info {
                display: flex;
            }
        }

        .client-status-badge {
            padding: 0.25rem 0.625rem;
            border-radius: var(--radius-full);
            font-size: 0.75rem;
            font-weight: 500;
        }

        .client-status-active {
            background: var(--clr-primary);
            color: var(--clr-text-on-primary);
        }

        .client-status-inactive {
            background: #fee2e2;
            color: #991b1b;
        }

        :root[data-theme="dark"] .client-status-active {
            background: #064e3b;
            color: #6ee7b7;
        }

        :root[data-theme="dark"] .client-status-inactive {
            background: #7f1d1d;
            color: #fca5a5;
        }
        
        .glass-card {
            background: var(--clr-card-bg); /* Subtle background difference */
            border-radius: var(--radius-lg);  /* Consistent 16px radius */
            padding: 24px;                  /* Consistent internal padding */
            border: none;                   /* Ensure no border */
            box-shadow: none;               /* Ensure no shadow */
        }
        
        .glass-card:hover {
            box-shadow: none;
            transform: none;
        }
        
        .range-btn {
            border: 1px solid var(--clr-border);
            border-radius: var(--radius-md);
            transition: all 0.2s ease;
            padding: 8px 16px; /* Was 12px 20px */
            font-weight: 500;
            background-color: transparent;
            color: var(--clr-text-main);
        }
        
        .range-btn:hover {
            background-color: rgba(var(--clr-primary-rgb), 0.1);
            border-color: rgba(var(--clr-primary-rgb), 0.4);
        }
        
        .range-btn.active {
            background: var(--clr-primary);
            border: 1px solid var(--clr-primary);
            color: var(--clr-text-on-primary) !important;
            box-shadow: none;
        }
        
        .chart-container {
            position: relative;
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
        button:not(.range-btn):not(.tab-btn):not(.theme-toggle):not(.logout-btn) {
            background: rgba(var(--clr-primary-rgb), 0.2);
            color: var(--clr-primary);
            border: 1px solid rgba(var(--clr-primary-rgb), 0.3);
            padding: 10px 20px;
            border-radius: var(--radius-md);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
        }

        button:not(.range-btn):not(.tab-btn):not(.theme-toggle):not(.logout-btn):hover {
            background: rgba(var(--clr-primary-rgb), 0.3);
            border-color: rgba(var(--clr-primary-rgb), 0.5);
            transform: none;
            opacity: 1;
        }

        select {
            border: 1px solid var(--clr-border);
            background-color: var(--clr-card-bg);
            color: var(--clr-text-main);
            padding: 12px 16px;
            border-radius: var(--radius-md);
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
            padding: 20px 24px;
            border-radius: var(--radius-lg);
        }

        .header-section {
            padding: 0;
        }

        .chart-section {
            padding: 0;
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
            gap: 8px; /* Was 12px */
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
            border-radius: var(--radius-md);
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

        /* Tab Styles - Flat & Elegant */
        .tab-buttons {
            display: flex;
            gap: 0;
            background: transparent;
            padding: 0;
            border: none;
            border-bottom: 1px solid var(--clr-border);
            width: fit-content;
        }

        .tab-btn {
            padding: 12px 24px;
            font-weight: 500;
            font-size: 14px;
            border-radius: 0;
            cursor: pointer;
            border: none;
            background: transparent;
            color: var(--clr-text-sub);
            transition: all 0.2s ease;
            position: relative;
            border-bottom: 2px solid transparent;
            margin-bottom: -1px;
        }

        .tab-btn:hover {
            color: var(--clr-text-main);
            background: transparent;
        }

        .tab-btn.active {
            background: transparent;
            color: var(--clr-primary);
            border-bottom-color: var(--clr-primary);
            box-shadow: none;
        }

        /* Alpine.js x-cloak - hide elements until Alpine is ready */
        [x-cloak] {
            display: none !important;
        }


        /* Comments Table Styles */
        .comments-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 24px;
        }
        .comments-table th, .comments-table td {
            padding: 12px 16px;
            text-align: left;
            border-bottom: 1px solid var(--clr-border);
            font-size: 14px;
        }
        .comments-table th {
            font-weight: 600;
            color: var(--clr-text-main);
        }
        .comments-table td {
            color: var(--clr-text-sub);
        }
        .comments-table tbody tr:last-child td {
            border-bottom: none;
        }
        .comments-table .comment-content {
            white-space: pre-wrap;
            word-break: break-word;
        }

        /* Chart Container Layout */
        .charts-container {
            display: flex;
            flex-direction: column; /* Changed from row to column */
            gap: 24px; /* Increased gap for better vertical spacing */
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
        /* This is no longer needed as the default is now column */
        /* @media (max-width: 1024px) {
            .charts-container {
                flex-direction: column;
                gap: 24px;
            }

            .chart-wrapper {
                width: 100%;
            }
        } */

        /* Theme Toggle Button in Navbar */
        .theme-toggle {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem;
            border-radius: var(--radius-md);
            background: transparent;
            border: 1px solid var(--clr-border);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .theme-toggle:hover {
            background: rgba(var(--clr-accent-rgb, 188, 108, 37), 0.1);
            border-color: var(--clr-accent);
        }

        .theme-toggle svg {
            width: 1.25rem;
            height: 1.25rem;
            stroke: var(--clr-text-main);
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
            fill: none;
            transition: stroke 0.2s ease;
        }

        .theme-toggle:hover svg {
            stroke: var(--clr-accent);
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

        .logout-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem;
            border-radius: var(--radius-md);
            background: transparent;
            border: 1px solid var(--clr-border);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .logout-btn:hover {
            background: rgba(var(--clr-accent-rgb, 188, 108, 37), 0.1);
            border-color: var(--clr-accent);
        }

        .logout-btn svg {
            width: 1.25rem;
            height: 1.25rem;
            stroke: var(--clr-text-main);
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
            fill: none;
            transition: stroke 0.2s ease;
        }

        .logout-btn:hover svg {
            stroke: var(--clr-accent);
        }

        .project-dropdown {
            position: relative;
            display: inline-block;
        }

        .project-dropdown-button {
            border: 1px solid var(--clr-border);
            background-color: var(--clr-card-bg);
            color: var(--clr-text-main);
            padding: 8px 16px;
            border-radius: var(--radius-md);
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            justify-content: space-between;
            align-items: center;
            white-space: nowrap;
            gap: 0.5rem;
        }

        .project-dropdown-button:focus,
        .project-dropdown-button:hover {
            border-color: var(--clr-primary);
            box-shadow: 0 0 0 3px rgba(var(--clr-primary-rgb), 0.1);
        }

        .project-dropdown-menu {
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            width: 100%;
            background-color: var(--clr-card-bg);
            border: 1px solid var(--clr-border);
            border-radius: var(--radius-md);
            box-shadow: 0 4px 12px var(--clr-shadow);
            z-index: 10;
            max-height: 200px;
            overflow-y: auto;
        }

        .project-dropdown-item {
            padding: 10px 16px;
            cursor: pointer;
            color: var(--clr-text-sub);
        }

        .project-dropdown-item:hover,
        .project-dropdown-item.selected {
            background-color: rgba(var(--clr-primary-rgb), 0.1);
            color: var(--clr-text-main);
        }

    </style>
</head>
<body class="gradient-bg">
    <!-- Sticky Navbar -->
    <nav class="navbar">
        <div class="navbar-content">
            <!-- Logo/Brand -->
            <div class="navbar-brand">
                <img src="{{ asset('img/mntl.svg') }}" alt="MNTL Logo" style="height: 32px; width: auto;">
            </div>

            <!-- Right Actions -->
            <div class="navbar-actions" style="margin-left: auto;">
                <!-- Client Info (hidden on mobile) -->
                <div class="navbar-client-info">
                    <div class="flex items-center gap-2 text-[var(--clr-text-sub)]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>{{ $client->uuid }}</span>
                    </div>
                    <span class="client-status-badge {{ $client->is_active ? 'client-status-active' : 'client-status-inactive' }}">
                        {{ $client->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>

                <!-- Theme Toggle -->
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

                <!-- Logout Button -->
                <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                    @csrf
                    <button type="submit" class="logout-btn" title="Logout">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                            <polyline points="16,17 21,12 16,7"/>
                            <line x1="21" y1="12" x2="9" y2="12"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="dashboard()">
        <!-- Header + Filters Container -->
        <div class="glass-card mb-6">
            <div class="flex items-center justify-between gap-8 flex-wrap">
                <!-- Title & Subtitle -->
                <div>
                    <h1 class="font-bold text-xl" style="color: var(--clr-primary)">
                        Prompt Quality Analytics
                    </h1>
                    <p class="mt-1 text-xs font-normal" style="color: var(--clr-text-sub)">
                        Real-time insights into prompt effectiveness
                    </p>
                </div>

                <!-- Right-aligned controls -->
                <div class="flex items-center gap-4">
                    <!-- Time Range Buttons -->
                    <div class="button-group">
                        <template x-for="opt in rangeOptions" :key="opt.value">
                            <button @click="setRange(opt.value)"
                                class="font-medium range-btn"
                                :class="range === opt.value ? 'active' : ''"
                                x-text="opt.label"></button>
                        </template>
                    </div>

                    <!-- Project Dropdown -->
                    <div x-data="{ projectDropdownOpen: false }" class="project-dropdown" @click.away="projectDropdownOpen = false">
                        <button @click="projectDropdownOpen = !projectDropdownOpen" class="project-dropdown-button">
                            <span x-text="project || 'All Projects'"></span>
                            <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': projectDropdownOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="projectDropdownOpen" x-transition class="project-dropdown-menu" x-cloak>
                            <div @click="setProject('')" class="project-dropdown-item" :class="{ 'selected': !project }">All Projects</div>
                            <template x-for="opt in projectOptions" :key="opt">
                                <div @click="setProject(opt)" class="project-dropdown-item" :class="{ 'selected': project === opt }" x-text="opt"></div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Content Area with Tabs -->
        <div x-init="$watch('tab', value => { if (value === 'analytics') initCharts() })">
            <!-- Tab Buttons -->
            <div class="mb-6">
                <div class="tab-buttons">
                    <button @click="tab = 'analytics'" class="tab-btn" :class="{ 'active': tab === 'analytics' }">Analytics</button>
                    <button @click="tab = 'comments'" class="tab-btn" :class="{ 'active': tab === 'comments' }">Comments</button>
                </div>
            </div>

            <!-- Analytics Content -->
            <div x-show="tab === 'analytics'" x-cloak>
                <div class="charts-container">

                    <!-- ===== OVERALL STATS SECTION ===== -->
                    <div class="chart-wrapper">
                        <div class="glass-card flex flex-col">
                            <div class="chart-section flex flex-col grow">
                                <div class="flex items-start justify-between mb-8 flex-wrap gap-6">
                                    <div class="flex-1 min-w-0">
                                        <h2 class="text-3xl font-bold text-[var(--clr-text-main)] mb-3">
                                            Overall Project Performance
                                        </h2>
                                        <p class="text-lg text-[var(--clr-text-sub)] font-normal leading-relaxed">
                                            Complete historical overview of prompt quality across all time periods.
                                        </p>
                                        <div class="mt-6 text-sm space-y-2">
                                            <div class="flex items-center gap-2">
                                                <span class="font-semibold text-[var(--clr-text-main)] w-32">Total Records:</span>
                                                <span class="text-[var(--clr-text-sub)]" x-text="overallStats?.total_records || 0"></span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="font-semibold text-[var(--clr-text-main)] w-32">Projects:</span>
                                                <span class="text-[var(--clr-text-sub)]" x-text="overallStats?.total_projects || 0"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="chart-container grow">
                                    <canvas id="overallChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ===== TIME SERIES SECTION (Show when range is 'all') ===== -->
                    @if($range === 'all')
                    <div class="chart-wrapper">
                        <div class="glass-card flex flex-col">
                            <div class="chart-section flex flex-col grow">
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
                                                <span class="text-[var(--clr-text-sub)]" x-text="getCurrentRangeLabel()"></span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="font-semibold text-[var(--clr-text-main)]">Records:</span>
                                                <span class="text-[var(--clr-text-sub)]" x-text="timeSeriesStats?.total_records || 0"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="chart-container grow">
                                    <canvas id="timeSeriesChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- ===== GRANULAR TIME-SERIES SECTION (Show when range is not 'all') ===== -->
                    @if($showGranularChart)
                    <div class="chart-wrapper">
                        <div class="glass-card flex flex-col">
                            <div class="chart-section flex flex-col grow">
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
                                                <span class="text-[var(--clr-text-sub)]" x-text="getCurrentRangeLabel()"></span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="font-semibold text-[var(--clr-text-main)]">Data Points:</span>
                                                <span class="text-[var(--clr-text-sub)]" x-text="granularStats?.length || 0"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="chart-container grow">
                                    <canvas id="granularChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- ===== BUBBLE CHART SECTION ===== -->
                    <div class="chart-wrapper">
                        <div class="glass-card flex flex-col">
                            <div class="chart-section flex flex-col grow">
                                <div class="flex items-start justify-between mb-8 flex-wrap gap-6">
                                    <div class="flex-1 min-w-0">
                                        <h2 class="text-3xl font-bold text-[var(--clr-text-main)] mb-3">
                                            Prompt Relationship Analysis
                                        </h2>
                                        <p class="text-lg text-[var(--clr-text-sub)] font-normal leading-relaxed">
                                            Visualizing the correlation between prompt effectiveness, ambiguity, and confusion.
                                        </p>
                                    </div>
                                </div>
                                <div class="chart-container grow">
                                    <canvas id="bubbleChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Comments Content -->
            <div x-show="tab === 'comments'" x-cloak>
                <div class="glass-card">
                    <h2 class="text-2xl font-bold text-[var(--clr-text-main)] mb-1">
                        Recent Comments
                    </h2>
                    <p class="text-md text-[var(--clr-text-sub)] font-normal leading-relaxed mb-6">
                        Latest 50 comments from the selected time range.
                    </p>
                    @if(isset($comments) && $comments->isNotEmpty())
                        <div class="overflow-x-auto">
                            <table class="comments-table">
                                <thead>
                                    <tr>
                                        <th style="width: 15%;">Project</th>
                                        <th style="width: 65%;">Comment</th>
                                        <th style="width: 20%;">Timestamp</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($comments as $comment)
                                        <tr>
                                            <td>{{ $comment->project }}</td>
                                            <td class="comment-content">{{ $comment->comments }}</td>
                                            <td>{{ \Carbon\Carbon::parse($comment->created_at)->format('d M Y, H:i:s') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center text-[var(--clr-text-sub)] py-8">
                            No comments found for the selected period.
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

<script>
    // ===== THEME MANAGEMENT =====
    // This part remains unchanged as it's global
    (function initTheme() {
        const savedTheme = localStorage.getItem('theme') || 'dark';
        document.documentElement.setAttribute('data-theme', savedTheme);
    })();

    function toggleTheme() {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        // The dashboard component will handle chart color updates
        window.dispatchEvent(new CustomEvent('theme-changed', { detail: { theme: newTheme } }));
    }

    // ===== ALPINE DASHBOARD COMPONENT =====
    function dashboard() {
        return {
            // --- STATE ---
            tab: 'analytics',
            range: @json($range),
            project: @json($selectedProject ?? ''),

            // --- STATIC DATA ---
            projectOptions: @json($availableProjects),
            rangeOptions: [
                {value:'30m',label:'30 Min'}, {value:'1h',label:'1 Hour'},
                {value:'1d',label:'1 Day'}, {value:'1w',label:'1 Week'},
                {value:'1m',label:'1 Month'}, {value:'all',label:'All Time'},
            ],
            
            // --- SERVER-SIDE DATA ---
            overallStats: @json($overallStats),
            timeSeriesStats: @json($timeSeriesStats),
            granularStats: @json($granularStats),
            bubbleChartData: @json($bubbleChartData),
            showGranularChart: @json($showGranularChart),

            // --- CHART INSTANCES ---
            charts: {},

            // --- INITIALIZATION ---
            init() {
                if (this.tab === 'analytics') {
                    this.initCharts();
                }
                
                window.addEventListener('theme-changed', (e) => {
                    this.updateChartColors(e.detail.theme);
                });
            },
            
            // --- METHODS ---
            setRange(r) {
                if (r !== this.range) {
                    const url = new URL(window.location.href);
                    url.searchParams.set('range', r);
                    if (this.project) url.searchParams.set('project', this.project);
                    window.location = url;
                }
            },
            setProject(p) {
                this.project = p;
                this.updateProject();
            },
            updateProject() {
                const url = new URL(window.location.href);
                if (this.project) {
                    url.searchParams.set('project', this.project);
                } else {
                    url.searchParams.delete('project');
                }
                if (this.range) url.searchParams.set('range', this.range);
                window.location = url;
            },
            getCurrentRangeLabel() {
                const labels = {'30m': 'Last 30 Minutes', '1h': 'Last Hour', '1d': 'Last Day', '1w': 'Last Week', '1m': 'Last Month', 'all': 'All Time'};
                return labels[this.range] || 'Last Day';
            },
            
            // --- CHART LOGIC ---
            destroyCharts() {
                Object.values(this.charts).forEach(chart => {
                    if (chart) chart.destroy();
                });
                this.charts = {};
            },

            initCharts() {
                this.$nextTick(() => {
                    this.destroyCharts();
                    
                    const theme = localStorage.getItem('theme') || 'dark';
                    const colors = this.getThemeColors(theme);
                    
                    this.createOverallChart(colors);
                    if (this.range === 'all') this.createTimeSeriesChart(colors);
                    if (this.showGranularChart) this.createGranularChart(colors);
                    this.createBubbleChart(colors);
                });
            },

            updateChartColors(theme) {
                const colors = this.getThemeColors(theme);
                Object.values(this.charts).forEach(chart => {
                    if (chart) {
                        chart.options.scales.y.ticks.color = colors.textColor;
                        chart.options.scales.y.grid.borderColor = colors.borderColor;
                        chart.options.scales.y.grid.color = colors.gridColor;
                        chart.options.scales.x.ticks.color = colors.textColor;
                        if(chart.options.scales.x.title) chart.options.scales.x.title.color = colors.textColor;
                        if(chart.options.scales.y.title) chart.options.scales.y.title.color = colors.textColor;
                        
                        // Update tooltip colors
                        chart.options.plugins.tooltip.backgroundColor = theme === 'dark' ? 'rgba(36, 43, 61, 0.95)' : 'rgba(255, 255, 255, 0.95)';
                        chart.options.plugins.tooltip.titleColor = colors.textMain;
                        chart.options.plugins.tooltip.bodyColor = colors.textSub;
                        chart.options.plugins.tooltip.borderColor = colors.borderColor;

                        chart.update();
                    }
                });
            },

            getThemeColors(theme) {
                const isDark = theme === 'dark';
                return {
                    textColor: isDark ? '#9ca3af' : '#64748b',
                    textMain: isDark ? '#e5e7eb' : '#1f2937',
                    textSub: isDark ? '#9ca3af' : '#4b5563',
                    borderColor: isDark ? '#2d3748' : '#e2e8f0',
                    gridColor: isDark ? 'rgba(45, 55, 72, 0.3)' : 'rgba(200,200,200,0.08)'
                };
            },

            createOverallChart(colors) {
                const ctx = document.getElementById('overallChart')?.getContext('2d');
                if (!ctx) return;
                
                const primaryColor = getComputedStyle(document.documentElement).getPropertyValue('--clr-primary').trim();
                const accentColor = getComputedStyle(document.documentElement).getPropertyValue('--clr-accent').trim();
                const ambiguityColor = getComputedStyle(document.documentElement).getPropertyValue('--clr-ambiguity').trim();

                this.charts.overall = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Overall Performance'],
                        datasets: [
                            { label: 'Avg Efektivitas', data: [this.overallStats?.avg_quality || 0], backgroundColor: 'rgba(63, 125, 88, 0.7)', borderRadius: 8 },
                            { label: 'Avg Membingungkan', data: [this.overallStats?.avg_confusion || 0], backgroundColor: 'rgba(239, 150, 81, 0.7)', borderRadius: 8 },
                            { label: 'Avg Ambiguity', data: [this.overallStats?.avg_ambiguity || 0], backgroundColor: 'rgba(217, 70, 91, 0.7)', borderRadius: 8 }
                        ]
                    },
                    options: this.getCommonChartOptions(colors)
                });
            },

            createTimeSeriesChart(colors) {
                 const ctx = document.getElementById('timeSeriesChart')?.getContext('2d');
                if (!ctx) return;

                this.charts.timeSeries = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Current Period'],
                        datasets: [
                            { label: 'Avg Efektivitas', data: [this.timeSeriesStats?.avg_quality || 0], backgroundColor: 'rgba(63, 125, 88, 0.7)', borderRadius: 8 },
                            { label: 'Avg Membingungkan', data: [this.timeSeriesStats?.avg_confusion || 0], backgroundColor: 'rgba(239, 150, 81, 0.7)', borderRadius: 8 },
                        ]
                    },
                    options: this.getCommonChartOptions(colors)
                });
            },

            createGranularChart(colors) {
                const ctx = document.getElementById('granularChart')?.getContext('2d');
                if (!ctx || !this.granularStats || this.granularStats.length === 0) return;

                const primaryColor = getComputedStyle(document.documentElement).getPropertyValue('--clr-primary').trim();
                const accentColor = getComputedStyle(document.documentElement).getPropertyValue('--clr-accent').trim();
                const ambiguityColor = getComputedStyle(document.documentElement).getPropertyValue('--clr-ambiguity').trim();
                
                const formatTimestamp = (timestamp, range) => {
                    const date = new Date(timestamp);
                    switch(range) {
                        case '30m': case '1h': return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false });
                        case '1d': return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: false });
                        case '1w': return date.toLocaleDateString('en-US', { day: '2-digit', month: 'short' }) + ' ' + date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: false });
                        case '1m': return date.toLocaleDateString('en-US', { day: '2-digit', month: 'short' });
                        default: return timestamp;
                    }
                };
                
                const pointRadius = this.granularStats.length > 100 ? 1 : (this.granularStats.length > 50 ? 2 : 3);

                this.charts.granular = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: this.granularStats.map(item => formatTimestamp(item.timestamp, this.range)),
                        datasets: [
                            { label: 'Avg Efektivitas', data: this.granularStats.map(item => item.avg_quality), borderColor: primaryColor, backgroundColor: 'rgba(63, 125, 88, 0.1)', fill: true, tension: 0.4, pointRadius: pointRadius },
                            { label: 'Avg Membingungkan', data: this.granularStats.map(item => item.avg_confusion), borderColor: accentColor, backgroundColor: 'rgba(239, 150, 81, 0.1)', fill: true, tension: 0.4, pointRadius: pointRadius },
                            { label: 'Avg Ambiguity', data: this.granularStats.map(item => item.avg_ambiguity), borderColor: ambiguityColor, backgroundColor: 'rgba(217, 70, 91, 0.1)', fill: true, tension: 0.4, pointRadius: pointRadius },
                        ]
                    },
                    options: this.getCommonChartOptions(colors, true)
                });
            },
            
            createBubbleChart(colors) {
                const ctx = document.getElementById('bubbleChart')?.getContext('2d');
                if (!ctx || !this.bubbleChartData || this.bubbleChartData.length === 0) return;

                const data = this.bubbleChartData.map(item => ({
                    x: item.efektivitas,
                    y: item.ambiguous,
                    r: item.membingungkan / 4
                }));

                this.charts.bubble = new Chart(ctx, {
                    type: 'bubble',
                    data: {
                        datasets: [{
                            label: 'Prompt Data',
                            data: data,
                            backgroundColor: 'rgba(63, 125, 88, 0.6)',
                        }]
                    },
                    options: {
                        ...this.getCommonChartOptions(colors),
                        plugins: {
                            ...this.getCommonChartOptions(colors).plugins,
                            legend: { display: false },
                            tooltip: {
                                ...this.getCommonChartOptions(colors).plugins.tooltip,
                                callbacks: {
                                    label: function(context) {
                                        const dataPoint = context.raw;
                                        return `Effectiveness: ${dataPoint.x}, Ambiguity: ${dataPoint.y}, Confusion: ${dataPoint.r * 4}`;
                                    }
                                }
                            }
                        }
                    }
                });
            },

            getCommonChartOptions(colors, isLineChart = false) {
                return {
                    animation: false,
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: true, position: 'top', labels: { usePointStyle: true, padding: 20, boxWidth: 12 } },
                        tooltip: {
                            backgroundColor: colors.isDark ? 'rgba(36, 43, 61, 0.95)' : 'rgba(255, 255, 255, 0.95)',
                            titleColor: colors.textMain, bodyColor: colors.textSub,
                            borderColor: colors.borderColor, borderWidth: 1, cornerRadius: 12, padding: 12,
                            titleFont: { weight: '600' }, bodyFont: { weight: '500' }
                        }
                    },
                    scales: {
                        y: { beginAtZero: true, max: 100, ticks: { stepSize: 10, color: colors.textColor, font: { weight: 500 }, padding: 12 }, grid: { borderColor: colors.borderColor, color: colors.gridColor } },
                        x: { grid: { display: isLineChart ? false : true, drawOnChartArea: isLineChart ? true : false, }, ticks: { display: isLineChart, color: colors.textColor, font: { weight: 500 }, padding: 12, maxTicksLimit: 20, autoSkip: true } }
                    }
                };
            }
        }
    }
</script>

<footer class="text-center py-4 mt-8">
    <p class="text-sm text-[var(--clr-text-sub)]">
        Powered by <a href="https://www.nrapken.dev" target="_blank" rel="noopener noreferrer" class="font-semibold text-[var(--clr-primary)] hover:underline">nrapken.dev</a>
    </p>
</footer>

</body>
</html> 