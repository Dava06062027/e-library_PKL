<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peminjamans', function (Blueprint $table) {
            $table->string('transaction_number', 50)->nullable()->unique()->after('id');
        });

        Schema::table('pengembalians', function (Blueprint $table) {
            $table->string('transaction_number', 50)->nullable()->unique()->after('id');
        });

        Schema::table('perpanjangans', function (Blueprint $table) {
            $table->string('transaction_number', 50)->nullable()->unique()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('peminjamans', function (Blueprint $table) {
            $table->dropColumn('transaction_number');
        });

        Schema::table('pengembalians', function (Blueprint $table) {
            $table->dropColumn('transaction_number');
        });

        Schema::table('perpanjangans', function (Blueprint $table) {
            $table->dropColumn('transaction_number');
        });
    }
};
