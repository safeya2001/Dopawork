<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['credit', 'debit', 'hold', 'release', 'refund', 'withdrawal', 'deposit']);
            $table->decimal('amount', 10, 3); // JOD
            $table->decimal('balance_before', 10, 3);
            $table->decimal('balance_after', 10, 3);
            $table->string('description')->nullable();
            $table->string('description_ar')->nullable();
            $table->nullableMorphs('transactionable', 'wt_transactionable_index'); // polymorphic - order, escrow, etc.
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])->default('completed');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
