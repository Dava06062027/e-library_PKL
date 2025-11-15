<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengembalians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_peminjaman_item')->constrained('peminjaman_items')->onDelete('cascade');
            $table->foreignId('id_officer')->constrained('users')->onDelete('cascade');
            $table->date('tanggal_kembali_aktual');
            $table->enum('kondisi_kembali', ['Baik', 'Cukup', 'Rusak', 'Hilang']);
            $table->integer('denda_keterlambatan')->default(0);
            $table->integer('denda_kerusakan')->default(0);
            $table->integer('total_denda')->default(0);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengembalians');
    }
};
