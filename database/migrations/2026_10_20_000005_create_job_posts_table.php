<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('description');
            $table->longText('requirements')->nullable();
            $table->longText('benefits')->nullable();
            $table->unsignedBigInteger('salary_min')->nullable();
            $table->unsignedBigInteger('salary_max')->nullable();
            $table->boolean('salary_negotiable')->default(false);
            $table->enum('type', ['full-time', 'part-time', 'remote', 'internship', 'hybrid'])->default('full-time');
            $table->enum('experience_level', ['all', 'intern', 'fresher', 'junior', 'middle', 'senior', 'lead'])->default('middle');
            $table->enum('status', ['pending', 'approved', 'rejected', 'closed'])->default('pending');
            $table->boolean('is_featured')->default(false);
            $table->date('deadline');
            $table->unsignedInteger('views_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_posts');
    }
};
