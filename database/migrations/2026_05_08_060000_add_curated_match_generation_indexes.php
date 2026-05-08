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
        Schema::table('curated_matches_windows', function (Blueprint $table) {
            $table->unique(['user_id', 'starts_at', 'ends_at'], 'cmw_user_period_unique');
            $table->index(['status', 'starts_at'], 'cmw_status_starts_idx');
        });

        Schema::table('curated_matches', function (Blueprint $table) {
            $table->unique(
                ['curated_matches_window_id', 'user_id'],
                'curated_matches_window_user_unique'
            );
            $table->index(['user_id', 'rank_score'], 'curated_matches_user_score_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('curated_matches', function (Blueprint $table) {
            $table->dropUnique('curated_matches_window_user_unique');
            $table->dropIndex('curated_matches_user_score_idx');
        });

        Schema::table('curated_matches_windows', function (Blueprint $table) {
            $table->dropUnique('cmw_user_period_unique');
            $table->dropIndex('cmw_status_starts_idx');
        });
    }
};
