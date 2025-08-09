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
        Schema::table('questions', function (Blueprint $table) {
            $table->enum('level', ['Nhận biết', 'Thông hiểu', 'Vận dụng', 'Vận dụng cao'])
              ->default('Nhận biết')
              ->after('answer');

            $table->integer('order')
                  ->default(1)
                  ->after('level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn(['level', 'order']);
        });
    }
};
