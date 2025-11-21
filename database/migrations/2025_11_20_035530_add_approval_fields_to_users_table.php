<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Field untuk tracking approval
            $table->unsignedBigInteger('approved_by')->nullable()->after('address');
            $table->timestamp('approved_at')->nullable()->after('approved_by');

            // Field untuk kartu member yang di-generate
            $table->string('member_card_photo')->nullable()->after('approved_at');

            // Add foreign key
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['approved_by', 'approved_at', 'member_card_photo']);
        });
    }
};
