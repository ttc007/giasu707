<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('book_variations', function (Blueprint $table) {
            $table->unsignedBigInteger('book_des_id')->nullable()->after('move');
        });

        DB::statement("
            UPDATE book_variations v
            JOIN books b ON b.book_variation_id = v.id
            SET v.book_des_id = b.id
        ");

        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn('book_variation_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('book_variations', function (Blueprint $table) {
            $table->dropColumn('book_des_id');
        });
    }
};
