<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('proposal_id')->constrained('project_proposals')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('amount', 10, 3); // JOD
            $table->date('due_date')->nullable();
            $table->enum('status', ['pending','in_progress','submitted','approved','revision_requested'])->default('pending');
            $table->text('delivery_note')->nullable(); // freelancer's delivery message
            $table->text('revision_note')->nullable(); // client's revision request
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_milestones');
    }
};
