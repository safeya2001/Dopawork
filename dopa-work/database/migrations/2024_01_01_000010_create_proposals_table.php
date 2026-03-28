<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Custom job postings by clients that freelancers bid on
        Schema::create('job_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('title');
            $table->string('title_ar')->nullable();
            $table->text('description');
            $table->text('description_ar')->nullable();
            $table->json('skills_required')->nullable();
            $table->decimal('budget_min', 10, 3)->nullable(); // JOD
            $table->decimal('budget_max', 10, 3)->nullable(); // JOD
            $table->enum('budget_type', ['fixed', 'hourly'])->default('fixed');
            $table->integer('duration_days')->nullable();
            $table->enum('experience_level', ['entry', 'intermediate', 'expert'])->default('intermediate');
            $table->enum('status', ['open', 'in_progress', 'closed', 'cancelled'])->default('open');
            $table->timestamp('expires_at')->nullable();
            $table->integer('proposals_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        // Freelancer proposals on job posts
        Schema::create('proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('freelancer_id')->constrained('users')->cascadeOnDelete();
            $table->text('cover_letter');
            $table->decimal('proposed_amount', 10, 3); // JOD
            $table->integer('delivery_days');
            $table->enum('status', ['pending', 'accepted', 'rejected', 'withdrawn'])->default('pending');
            $table->json('milestones_plan')->nullable(); // proposed milestone breakdown
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposals');
        Schema::dropIfExists('job_posts');
    }
};
