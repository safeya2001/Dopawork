<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['basic', 'standard', 'premium']);
            $table->string('name');
            $table->string('name_ar');
            $table->text('description');
            $table->text('description_ar');
            $table->decimal('price', 10, 3); // JOD
            $table->integer('delivery_days');
            $table->integer('revisions')->default(1);
            $table->json('features')->nullable(); // array of feature strings
            $table->json('features_ar')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_packages');
    }
};
