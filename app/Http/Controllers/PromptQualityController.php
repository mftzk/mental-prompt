<?php

namespace App\Http\Controllers;

use App\Models\PromptQuality;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PromptQualityController extends Controller
{
    /**
     * Store a newly created resource in storage.
     * Expect payload structure:
     * {
     *   "user_id": int,
     *   "project": string,
     *   "prompt_quality": {
     *       "efektivitas": int 0-100,
     *       "membingungkan": int 0-100
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
        ]);

        $pq = PromptQuality::create([
            'client_uuid' => $validated['client_uuid'],
            'project' => $validated['project'],
            'efektivitas' => $validated['prompt_quality']['efektivitas'],
            'membingungkan' => $validated['prompt_quality']['membingungkan'],
        ]);

        return response()->json(['success' => true, 'data' => $pq], 201);
    }

    /**
     * Display dashboard with aggregated statistics.
     */
    public function dashboard()
    {
        $range = request('range', '1d');
        $selectedProject = request('project', null);

        // Get all available projects for dropdown (ignoring range filter to show all)
        $availableProjects = DB::table('prompt_qualities as pq')
            ->distinct()
            ->pluck('project')
            ->filter()
            ->sort()
            ->values();

        // ===== OVERALL STATS (All Time) =====
        $overallQuery = DB::table('prompt_qualities as pq');
        if ($selectedProject) {
            $overallQuery->where('pq.project', $selectedProject);
        }

        $overallStats = (clone $overallQuery)
            ->select(
                DB::raw('AVG(pq.efektivitas) as avg_quality'),
                DB::raw('AVG(pq.membingungkan) as avg_confusion'),
                DB::raw('COUNT(*) as total_records'),
                DB::raw('COUNT(DISTINCT pq.project) as total_projects')
            )
            ->first();

        // ===== TIME SERIES STATS (With Time Filter) =====
        $timeSeriesQuery = DB::table('prompt_qualities as pq');

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

        return view('dashboard', [
            'overallStats'     => $overallStats,      // 👈 All-time stats
            'timeSeriesStats'  => $timeSeriesStats,   // 👈 Time-filtered stats
            'range'            => $range,
            'availableProjects' => $availableProjects,
            'selectedProject'   => $selectedProject,
        ]);
    }

    /**
     * Generate platform-specific SQL expression for timestamp bucketing.
     */
    private function getTimestampIntervalExpression(string $column, int $minutes, string $driver): string
    {
        return match ($driver) {
            'mysql' => "FROM_UNIXTIME(FLOOR(UNIX_TIMESTAMP($column) / ($minutes * 60)) * ($minutes * 60))",
            'pgsql' => "to_timestamp(floor(extract(epoch from $column) / ($minutes * 60)) * ($minutes * 60))",
            'sqlite' => "datetime(floor(strftime('%s', $column) / ($minutes * 60)) * ($minutes * 60), 'unixepoch')",
            default => "strftime('%Y-%m-%d %H:%M:00', $column)", // Fallback, may not be precise
        };
    }
} 