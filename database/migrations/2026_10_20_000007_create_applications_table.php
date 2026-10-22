<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_post_id')->constrained('job_posts')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('cv_id')->nullable()->constrained('cvs')->nullOnDelete();
            $table->text('cover_letter')->nullable();
            $table->enum('status', ['submitted', 'viewed', 'interview', 'accepted', 'rejected', 'withdrawn'])->default('submitted');
            $table->timestamp('applied_at')->useCurrent();
            $table->timestamps();

            $table->unique(['job_post_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
