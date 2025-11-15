<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perpanjangans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_peminjaman')->constrained('peminjamans')->onDelete('cascade');
            $table->foreignId('id_officer')->constrained('users')->onDelete('cascade');
            $table->date('tanggal_perpanjangan');
            $table->date('due_date_lama');
            $table->date('due_date_baru');
            $table->integer('biaya')->default(0);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perpanjangans');
    }
};
