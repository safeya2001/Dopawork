<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('freelancer_id')->constrained('users')->cascadeOnDelete();
            $table->text('cover_letter');
            $table->decimal('budget', 10, 3);  // JOD — freelancer's proposed amount
            $table->unsignedInteger('delivery_days');
            $table->enum('status', ['pending','accepted','rejected','withdrawn'])->default('pending');
            $table->timestamps();
            $table->unique(['project_id', 'freelancer_id']); // one proposal per project
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_proposals');
    }
};
