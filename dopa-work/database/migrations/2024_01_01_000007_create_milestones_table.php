<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('title_ar')->nullable();
            $table->text('description')->nullable();
            $table->decimal('amount', 10, 3); // JOD amount for this milestone
            $table->integer('sort_order')->default(0);
            $table->enum('status', ['pending', 'in_progress', 'delivered', 'approved', 'revision_requested'])->default('pending');
            $table->timestamp('due_date')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('delivery_note')->nullable();
            $table->json('attachments')->nullable(); // file paths
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('milestones');
    }
};
