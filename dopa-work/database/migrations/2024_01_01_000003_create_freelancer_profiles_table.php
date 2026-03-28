<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('freelancer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('professional_title')->nullable();
            $table->string('professional_title_ar')->nullable();
            $table->text('overview')->nullable();
            $table->text('overview_ar')->nullable();
            $table->json('skills')->nullable(); // ["PHP", "Laravel", ...]
            $table->json('languages')->nullable(); // [{"lang": "Arabic", "level": "Native"}, ...]
            $table->string('education')->nullable();
            $table->string('portfolio_url')->nullable();
            $table->json('portfolio_links')->nullable();
            $table->decimal('hourly_rate', 8, 3)->nullable(); // JOD
            $table->enum('experience_level', ['entry', 'intermediate', 'expert'])->default('entry');
            $table->integer('total_orders')->default(0);
            $table->integer('completed_orders')->default(0);
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->integer('total_reviews')->default(0);
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_available')->default(true);
            $table->string('member_since')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('freelancer_profiles');
    }
};
