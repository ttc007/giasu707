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
        Schema::table('views', function (Blueprint $table) {
            Schema::table('views', function (Blueprint $table) {
                $table->unsignedBigInteger('registration_id')->nullable()->after('id');
            });

            Schema::table('favorites', function (Blueprint $table) {
                $table->unsignedBigInteger('registration_id')->nullable()->after('id');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('views', function (Blueprint $table) {
            $table->dropColumn('registration_id');
            // Nếu tạo foreign key thì dùng $table->dropForeign(['registration_id']);
        });

        Schema::table('favorites', function (Blueprint $table) {
            $table->dropColumn('registration_id');
            // Nếu tạo foreign key thì dùng $table->dropForeign(['registration_id']);
        });
    }
};
