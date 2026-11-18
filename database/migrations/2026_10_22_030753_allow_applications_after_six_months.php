<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->index(['job_post_id', 'user_id', 'applied_at'], 'applications_recent_index');
            $table->dropUnique(['job_post_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::table('applications')->select('job_post_id', 'user_id')->groupBy('job_post_id', 'user_id')->havingRaw('COUNT(*) > 1')->exists()) {
            throw new RuntimeException('Cannot restore the old unique constraint while repeat applications exist.');
        }
        Schema::table('applications', function (Blueprint $table) {
            $table->unique(['job_post_id', 'user_id']);
            $table->dropIndex('applications_recent_index');
        });
    }
};
