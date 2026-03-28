<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('escrow_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique(); // DW-ESC-20240101-XXXX
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('freelancer_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('milestone_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 10, 3); // JOD held in escrow
            $table->decimal('platform_fee', 10, 3)->default(0);
            $table->decimal('freelancer_amount', 10, 3); // amount after fees
            $table->enum('status', [
                'held',       // money held in escrow
                'released',   // released to freelancer
                'refunded',   // refunded to client
                'disputed',   // under dispute resolution
            ])->default('held');
            $table->enum('payment_method', ['wallet', 'stripe', 'cliq', 'bank_transfer'])->default('wallet');
            $table->string('payment_reference')->nullable(); // external payment ID
            $table->timestamp('held_at')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamp('auto_release_at')->nullable(); // auto-release after X days
            $table->foreignId('released_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('escrow_transactions');
    }
};
