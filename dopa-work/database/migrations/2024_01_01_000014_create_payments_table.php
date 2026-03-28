<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique(); // DW-PAY-20240101-XXXX
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('escrow_id')->nullable()->constrained('escrow_transactions')->nullOnDelete();
            $table->decimal('amount', 10, 3); // JOD
            $table->enum('currency', ['JOD'])->default('JOD');
            $table->enum('payment_method', ['stripe', 'cliq', 'bank_transfer', 'wallet']);
            $table->enum('type', ['order_payment', 'withdrawal', 'deposit', 'refund', 'platform_fee']);
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'refunded'])->default('pending');
            $table->string('gateway_reference')->nullable(); // stripe charge id, cliq ref, etc.
            $table->json('gateway_response')->nullable();
            $table->string('cliq_alias')->nullable(); // for CliQ transfers
            $table->string('bank_account')->nullable(); // for bank transfers
            $table->text('notes')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
