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
        Schema::table('prompt_qualities', function (Blueprint $table) {
            // Fix the typo by renaming the column, if it exists with the wrong name
            if (Schema::hasColumn('prompt_qualities', 'ambigoues')) {
                $table->renameColumn('ambigoues', 'ambiguous');
            }

            // Ensure the 'comments' column exists
            if (!Schema::hasColumn('prompt_qualities', 'comments')) {
                $table->text('comments')->nullable()->after('ambiguous');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prompt_qualities', function (Blueprint $table) {
            // Revert the typo fix
            if (Schema::hasColumn('prompt_qualities', 'ambiguous')) {
                $table->renameColumn('ambiguous', 'ambigoues');
            }

            // Remove the 'comments' column if it exists
            if (Schema::hasColumn('prompt_qualities', 'comments')) {
                $table->dropColumn('comments');
            }
        });
    }
};
