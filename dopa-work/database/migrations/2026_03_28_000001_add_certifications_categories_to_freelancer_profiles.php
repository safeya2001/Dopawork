<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('freelancer_profiles', function (Blueprint $table) {
            $table->json('certifications')->nullable()->after('education');
            $table->json('category_ids')->nullable()->after('certifications');
        });

        Schema::create('portfolio_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('freelancer_profile_id')->constrained()->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->enum('type', ['image', 'pdf', 'link'])->default('link');
            $table->string('file_path')->nullable();
            $table->string('url')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_items');
        Schema::table('freelancer_profiles', function (Blueprint $table) {
            $table->dropColumn(['certifications', 'category_ids']);
        });
    }
};
