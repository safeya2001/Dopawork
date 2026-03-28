<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique(); // e.g. DW-2024-000001
            $table->foreignId('client_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('freelancer_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('service_id')->constrained()->restrictOnDelete();
            $table->foreignId('service_package_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('requirements')->nullable(); // client's requirements
            $table->enum('status', [
                'pending',        // awaiting freelancer acceptance
                'in_progress',    // active work
                'delivered',      // freelancer delivered
                'revision',       // client requested revision
                'completed',      // client accepted delivery
                'cancelled',      // cancelled
                'disputed'        // under dispute
            ])->default('pending');
            $table->decimal('subtotal', 10, 3); // JOD - service price
            $table->decimal('platform_fee', 10, 3); // JOD - 15%
            $table->decimal('total_amount', 10, 3); // JOD - subtotal + fee
            $table->decimal('freelancer_earnings', 10, 3); // JOD - subtotal - fee
            $table->integer('delivery_days');
            $table->timestamp('deadline')->nullable();
            $table->integer('revisions_allowed')->default(1);
            $table->integer('revisions_used')->default(0);
            $table->text('cancellation_reason')->nullable();
            $table->enum('cancelled_by', ['client', 'freelancer', 'admin'])->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
