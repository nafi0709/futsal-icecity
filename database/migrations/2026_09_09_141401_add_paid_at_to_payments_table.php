<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Tambahkan kolom paid_at jika belum ada
            if (!Schema::hasColumn('payments', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('status');
            }
            
            // Tambahkan kolom proof_uploaded_at jika belum ada
            if (!Schema::hasColumn('payments', 'proof_uploaded_at')) {
                $table->timestamp('proof_uploaded_at')->nullable()->after('paid_at');
            }
            
            // Tambahkan kolom payment_proof_path jika belum ada
            if (!Schema::hasColumn('payments', 'payment_proof_path')) {
                $table->string('payment_proof_path')->nullable()->after('qr_code_url');
            }
            
            // Tambahkan kolom payment_proof_url jika belum ada
            if (!Schema::hasColumn('payments', 'payment_proof_url')) {
                $table->string('payment_proof_url')->nullable()->after('payment_proof_path');
            }
            
            // Tambahkan kolom payment_notes jika belum ada
            if (!Schema::hasColumn('payments', 'payment_notes')) {
                $table->text('payment_notes')->nullable()->after('payment_proof_url');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['paid_at', 'proof_uploaded_at', 'payment_proof_path', 'payment_proof_url', 'payment_notes']);
        });
    }
};