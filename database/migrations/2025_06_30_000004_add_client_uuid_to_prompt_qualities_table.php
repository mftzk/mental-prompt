<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prompt_qualities', function (Blueprint $table) {
            // Safely drop FK & column if they exist
            if (Schema::hasColumn('prompt_qualities', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }

            if (!Schema::hasColumn('prompt_qualities', 'client_uuid')) {
                $table->uuid('client_uuid')->after('id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('prompt_qualities', function (Blueprint $table) {
            if (Schema::hasColumn('prompt_qualities', 'client_uuid')) {
                $table->dropColumn('client_uuid');
            }

            if (!Schema::hasColumn('prompt_qualities', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            }
        });
    }
}; 