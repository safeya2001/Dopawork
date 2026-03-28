<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->foreignId('subcategory_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('title');
            $table->string('title_ar');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('description_ar');
            $table->json('tags')->nullable();
            $table->string('cover_image')->nullable();
            $table->json('gallery')->nullable(); // array of image paths
            $table->enum('status', ['draft', 'active', 'paused', 'rejected', 'pending_review'])->default('pending_review');
            $table->text('rejection_reason')->nullable();
            $table->integer('delivery_days')->default(3);
            $table->integer('revisions')->default(1);
            $table->boolean('is_featured')->default(false);
            $table->integer('views')->default(0);
            $table->integer('orders_count')->default(0);
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->integer('reviews_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
