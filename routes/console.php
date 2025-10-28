<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('db:test', function () {
    $this->info('=== DATABASE CONNECTION TEST ===');

    // Show environment variables
    $this->line('Environment Variables:');
    $this->line('DB_CONNECTION: ' . env('DB_CONNECTION', 'not set'));
    $this->line('DB_HOST: ' . env('DB_HOST', 'not set'));
    $this->line('DB_PORT: ' . env('DB_PORT', 'not set'));
    $this->line('DB_DATABASE: ' . env('DB_DATABASE', 'not set'));
    $this->line('DB_USERNAME: ' . env('DB_USERNAME', 'not set'));
    $this->line('DB_PASSWORD: ' . (env('DB_PASSWORD') ? '***SET***' : 'NOT SET'));

    $this->line('');

    try {
        // Test basic connection
        DB::connection()->getPdo();
        $this->info('✅ Database connection successful!');

        // Test query
        $result = DB::select('SELECT 1 as test');
        $this->line('Test query result: ' . $result[0]->test);

        // Check tables
        $tables = DB::select('SHOW TABLES');
        $this->line('Available tables: ' . count($tables));
        if (count($tables) > 0) {
            $tableNames = array_map(function($table) {
                return array_values((array)$table)[0];
            }, array_slice($tables, 0, 5));
            $this->line('Tables: ' . implode(', ', $tableNames) . (count($tables) > 5 ? '...' : ''));
        }

    } catch (\Exception $e) {
        $this->error('❌ Database connection failed!');
        $this->error('Error: ' . $e->getMessage());

        // Additional troubleshooting
        $this->line('');
        $this->line('=== TROUBLESHOOTING ===');

        $host = env('DB_HOST', 'localhost');
        $port = env('DB_PORT', '3306');
        $this->line("Testing connectivity to $host:$port...");

        $connection = @fsockopen($host, $port, $errno, $errstr, 5);
        if ($connection) {
            $this->info('✅ Host:port is reachable');
            fclose($connection);
        } else {
            $this->error('❌ Host:port is not reachable');
            $this->error("Error: $errstr ($errno)");
        }
    }
})->purpose('Test database connection and show diagnostics');
