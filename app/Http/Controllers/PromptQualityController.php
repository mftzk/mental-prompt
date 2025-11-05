<?php

namespace App\Http\Controllers;

use App\Models\PromptQuality;
use App\Models\User;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PromptQualityController extends Controller
{
    /**
     * Store a newly created resource in storage.
     * Expect payload structure:
     * {
     *   "client_uuid": string (UUID),
     *   "project": string,
     *   "prompt_quality": {
     *       "efektivitas": int 0-100,
     *       "membingungkan": int 0-100,
     *       "ambiguous": int 0-100 (optional),
     *       "comments": string (optional)
     *   }
     * }
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_uuid' => ['required', 'uuid'],
            'project' => ['required', 'string', 'max:255'],
            'prompt_quality.efektivitas' => ['required', 'integer', 'between:0,100'],
            'prompt_quality.membingungkan' => ['required', 'integer', 'between:0,100'],
            'prompt_quality.ambiguous' => ['nullable', 'integer', 'between:0,100'],
            'prompt_quality.comments' => ['nullable', 'string', 'max:1000'],
        ]);

        // Validate that client UUID exists and is active
        $client = Client::where('uuid', $validated['client_uuid'])
                       ->where('is_active', true)
                       ->first();

        if (!$client) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid or inactive client UUID',
                'message' => 'The provided client UUID is not registered or has been deactivated.'
            ], 403);
        }

        $pq = PromptQuality::create([
            'client_uuid' => $validated['client_uuid'],
            'project' => $validated['project'],
            'efektivitas' => $validated['prompt_quality']['efektivitas'],
            'membingungkan' => $validated['prompt_quality']['membingungkan'],
            'ambiguous' => $validated['prompt_quality']['ambiguous'] ?? null,
            'comments' => $validated['prompt_quality']['comments'] ?? null,
        ]);

        return response()->json(['success' => true, 'data' => $pq], 201);
    }

    /**
     * Generate a new UUID for a client.
     * Public endpoint - no authentication required.
     */
    public function generateUuid()
    {
        $uuid = (string) Str::uuid();
        
        $client = Client::create([
            'uuid' => $uuid,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'uuid' => $client->uuid,
            'message' => 'UUID generated successfully. Please save this UUID securely.',
        ], 201);
    }

    /**
     * Show the login form.
     */
    public function showLogin()
    {
        // If already logged in, redirect to dashboard
        if (session()->has('client_uuid')) {
            return redirect()->route('dashboard');
        }
        
        return view('auth.client-login');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'uuid' => ['required', 'uuid'],
        ]);

        $client = Client::where('uuid', $validated['uuid'])
                       ->where('is_active', true)
                       ->first();

        if (!$client) {
            return back()->withErrors(['uuid' => 'Invalid or inactive client UUID.'])->withInput();
        }

        // Store client UUID in session
        session(['client_uuid' => $client->uuid]);

        return redirect()->route('dashboard');
    }

    /**
     * Handle logout request.
     */
    public function logout()
    {
        session()->forget('client_uuid');
        return redirect()->route('login')->with('message', 'Logged out successfully.');
    }

    /**
     * Display dashboard with aggregated statistics.
     */
    public function dashboard(Request $request)
    {
        // Get client UUID from session
        $clientUuid = session('client_uuid');
        
        // Get client data from request (set by middleware)
        $client = $request->attributes->get('client');

        $range = request('range', '1d');
        $selectedProject = request('project', null);

        // Get all available projects for dropdown - FILTERED BY CLIENT
        $availableProjects = DB::table('prompt_qualities as pq')
            ->where('pq.client_uuid', $clientUuid)
            ->distinct()
            ->pluck('project')
            ->filter()
            ->sort()
            ->values();

        // ===== OVERALL STATS (All Time) - FILTERED BY CLIENT =====
        $overallQuery = DB::table('prompt_qualities as pq')
            ->where('pq.client_uuid', $clientUuid);
        
        if ($selectedProject) {
            $overallQuery->where('pq.project', $selectedProject);
        }

        $overallStats = (clone $overallQuery)
            ->select(
                DB::raw('AVG(pq.efektivitas) as avg_quality'),
                DB::raw('AVG(pq.membingungkan) as avg_confusion'),
                DB::raw('AVG(pq.ambiguous) as avg_ambiguity'),
                DB::raw('COUNT(*) as total_records'),
                DB::raw('COUNT(DISTINCT pq.project) as total_projects')
            )
            ->first();

        // ===== TIME SERIES STATS (With Time Filter) - FILTERED BY CLIENT =====
        $timeSeriesQuery = DB::table('prompt_qualities as pq')
            ->where('pq.client_uuid', $clientUuid);

        // Apply time filtering
        if ($range !== 'all') {
            $from = match ($range) {
                '30m' => now()->subMinutes(30),
                '1h'  => now()->subHour(),
                '1d'  => now()->subDay(),
                '1w'  => now()->subWeek(),
                default => now()->subDay(),
            };
            $timeSeriesQuery->where('pq.created_at', '>=', $from);
        }

        if ($selectedProject) {
            $timeSeriesQuery->where('pq.project', $selectedProject);
        }

        $timeSeriesStats = (clone $timeSeriesQuery)
            ->select(
                DB::raw('AVG(pq.efektivitas) as avg_quality'),
                DB::raw('AVG(pq.membingungkan) as avg_confusion'),
                DB::raw('COUNT(*) as total_records')
            )
            ->first();

        // ===== GRANULAR TIME-SERIES DATA (For detailed charts) =====
        $granularStats = collect([]);
        $showGranularChart = false;
        
        if ($range !== 'all') {
            $granularStats = $this->getGranularTimeSeriesData($range, $selectedProject, $clientUuid);
            $showGranularChart = true;
        }

        // ===== BUBBLE CHART DATA =====
        $bubbleChartData = (clone $timeSeriesQuery)
            ->select('efektivitas', 'ambiguous', 'membingungkan')
            ->whereNotNull('ambiguous') // Hanya ambil data yang memiliki nilai ambiguous
            ->get();

        $comments = (clone $timeSeriesQuery)
            ->select('project', 'comments', 'created_at')
            ->whereNotNull('comments')
            ->where('comments', '!=', '')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return view('dashboard', [
            'client'            => $client,                // 👈 Client info
            'overallStats'      => $overallStats,          // 👈 All-time stats
            'timeSeriesStats'   => $timeSeriesStats,       // 👈 Time-filtered stats
            'granularStats'     => $granularStats,         // 👈 Granular time-series data
            'bubbleChartData'   => $bubbleChartData,       // 👈 Data untuk bubble chart
            'showGranularChart' => $showGranularChart,     // 👈 Flag to show/hide granular chart
            'range'             => $range,
            'availableProjects' => $availableProjects,
            'selectedProject'   => $selectedProject,
            'comments'          => $comments,
        ]);
    }

    /**
     * Generate platform-specific SQL expression for timestamp bucketing.
     * @param string $column The column name to bucket
     * @param int $seconds The interval in seconds
     * @param string $driver The database driver
     * @return string SQL expression for time bucketing
     */
    private function getTimestampIntervalExpression(string $column, int $seconds, string $driver): string
    {
        return match ($driver) {
            'mysql' => "FROM_UNIXTIME(FLOOR(UNIX_TIMESTAMP($column) / $seconds) * $seconds)",
            'pgsql' => "to_timestamp(floor(extract(epoch from $column) / $seconds) * $seconds)",
            'sqlite' => "datetime(floor(strftime('%s', $column) / $seconds) * $seconds, 'unixepoch')",
            default => "strftime('%Y-%m-%d %H:%M:00', $column)", // Fallback
        };
    }

    /**
     * Get granular time-series data with adaptive intervals based on range.
     * @param string $range Time range filter (30m, 1h, 1d, 1w, 1m)
     * @param string|null $project Project filter
     * @param string $clientUuid Client UUID filter
     * @return \Illuminate\Support\Collection
     */
    private function getGranularTimeSeriesData(string $range, ?string $project, string $clientUuid): \Illuminate\Support\Collection
    {
        // Define interval in seconds and time range for each option
        [$intervalSeconds, $from] = match ($range) {
            '30m' => [10, now()->subMinutes(30)],      // 10-second intervals
            '1h'  => [30, now()->subHour()],           // 30-second intervals
            '1d'  => [30, now()->subDay()],            // 30-second intervals
            '1w'  => [3600, now()->subWeek()],         // 1-hour intervals
            '1m'  => [86400, now()->subMonth()],       // 1-day intervals
            default => [30, now()->subDay()],
        };

        $driver = DB::connection()->getDriverName();
        $timestampExpr = $this->getTimestampIntervalExpression('pq.created_at', $intervalSeconds, $driver);

        $query = DB::table('prompt_qualities as pq')
            ->where('pq.client_uuid', $clientUuid)
            ->where('pq.created_at', '>=', $from);

        if ($project) {
            $query->where('pq.project', $project);
        }

        return $query
            ->select(
                DB::raw("$timestampExpr as timestamp"),
                DB::raw('AVG(pq.efektivitas) as avg_quality'),
                DB::raw('AVG(pq.membingungkan) as avg_confusion'),
                DB::raw('AVG(pq.ambiguous) as avg_ambiguity'),
                DB::raw('COUNT(*) as total_records')
            )
            ->groupBy('timestamp')
            ->orderBy('timestamp')
            ->get()
            ->map(function ($item) {
                return [
                    'timestamp' => $item->timestamp,
                    'avg_quality' => round((float)$item->avg_quality, 2),
                    'avg_confusion' => round((float)$item->avg_confusion, 2),
                    'avg_ambiguity' => round((float)$item->avg_ambiguity, 2),
                    'total_records' => $item->total_records
                ];
            });
    }
} 