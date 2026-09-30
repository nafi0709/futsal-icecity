<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('payment_proof_path')->nullable()->after('qr_code_url');
            $table->string('payment_proof_url')->nullable()->after('payment_proof_path');
            $table->text('payment_notes')->nullable()->after('payment_proof_url');
            $table->timestamp('proof_uploaded_at')->nullable()->after('payment_notes');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['payment_proof_path', 'payment_proof_url', 'payment_notes', 'proof_uploaded_at']);
        });
    }
};