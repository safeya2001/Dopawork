<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->string('proof_path')->nullable()->after('description_ar');
            $table->text('admin_note')->nullable()->after('proof_path');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete()->after('admin_note');
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
        });
    }

    public function down(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('reviewed_by');
            $table->dropColumn(['proof_path', 'admin_note', 'reviewed_at']);
        });
    }
};
