<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('title');
            $table->text('description');
            $table->enum('budget_type', ['fixed', 'hourly'])->default('fixed');
            $table->decimal('budget_min', 10, 3)->nullable(); // JOD
            $table->decimal('budget_max', 10, 3)->nullable(); // JOD
            $table->date('deadline')->nullable();
            $table->json('required_skills')->nullable();   // ["PHP","Laravel",...]
            $table->json('attachments')->nullable();       // stored file paths
            $table->string('preferred_location')->nullable(); // Amman, Irbid, etc.
            $table->enum('status', ['open','in_progress','completed','cancelled'])->default('open');
            $table->unsignedInteger('proposals_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
