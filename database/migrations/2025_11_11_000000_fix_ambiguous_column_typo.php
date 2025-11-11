<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if the column exists before trying to rename it
        if (Schema::hasColumn('prompt_qualities', 'ambigoues')) {
            Schema::table('prompt_qualities', function (Blueprint $table) {
                $table->renameColumn('ambigoues', 'ambiguous');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Check if the column exists before trying to rename it back
        if (Schema::hasColumn('prompt_qualities', 'ambiguous')) {
            Schema::table('prompt_qualities', function (Blueprint $table) {
                $table->renameColumn('ambiguous', 'ambigoues');
            });
        }
    }
};
