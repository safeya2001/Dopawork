<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disputes', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('order_id')->constrained()->restrictOnDelete();
            $table->foreignId('raised_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('against')->constrained('users')->restrictOnDelete();
            $table->string('subject');
            $table->text('description');
            $table->json('attachments')->nullable();
            $table->enum('status', ['open', 'under_review', 'resolved', 'closed'])->default('open');
            $table->enum('resolution', ['refund_client', 'release_freelancer', 'partial_split', 'no_action'])->nullable();
            $table->text('resolution_notes')->nullable();
            $table->decimal('client_refund_amount', 10, 3)->nullable();
            $table->decimal('freelancer_release_amount', 10, 3)->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disputes');
    }
};
