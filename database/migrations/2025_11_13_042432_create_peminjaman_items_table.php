<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjaman_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_peminjaman')->constrained('peminjamans')->onDelete('cascade');
            $table->foreignId('id_buku_item')->constrained('buku_items')->onDelete('cascade');
            $table->enum('kondisi_pinjam', ['Baik', 'Rusak', 'Hilang'])->default('Baik');
            $table->enum('status_item', ['Dipinjam', 'Dikembalikan', 'Hilang', 'Rusak'])->default('Dipinjam');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman_items');
    }
};
