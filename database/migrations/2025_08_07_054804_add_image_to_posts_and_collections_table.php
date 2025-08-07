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
        Schema::table('posts', function (Blueprint $table) {
            $table->string('image')->nullable()->after('content');
        });

        Schema::table('collections', function (Blueprint $table) {
            $table->string('image')->nullable()->after('description'); // nếu có cột 'description', nếu không thì xóa phần ->after()
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts_and_collections', function (Blueprint $table) {
            //
        });
    }
};
