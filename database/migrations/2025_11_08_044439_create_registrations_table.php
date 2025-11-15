<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->text('address');
            $table->string('phone')->nullable();
            $table->date('birth_date')->nullable();

            // Upload dokumen
            $table->string('id_document')->nullable(); // KTP/SIM/Passport
            $table->string('address_proof')->nullable(); // Bukti alamat

            // Status pendaftaran
            $table->enum('status', [
                'pending_verification',    // Baru submit, tunggu email verify
                'email_verified',          // Email sudah diverifikasi
                'under_review',           // Sedang direview petugas
                'document_requested',     // Minta dokumen tambahan
                'pending_approval',       // Tunggu approval officer
                'approved',               // Disetujui
                'rejected'                // Ditolak
            ])->default('pending_verification');

            // Verifikasi
            $table->string('verification_token')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('approved_at')->nullable();

            // Petugas yang handle
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();

            // Catatan
            $table->text('review_notes')->nullable(); // Catatan dari petugas
            $table->text('rejection_reason')->nullable(); // Alasan reject

            // Temporary digital card number
            $table->string('temp_card_number')->nullable()->unique();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
