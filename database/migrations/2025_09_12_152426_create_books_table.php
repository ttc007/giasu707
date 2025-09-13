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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->text('image_chess'); 
            $table->enum('color', ['red', 'green']);
            $table->string('move'); 
            $table->text('comment')->nullable();
            $table->unsignedBigInteger('opening_id')->nullable(); // liên kết với thế trận khai cuộc
            $table->integer('step')->default(1);
            $table->unsignedBigInteger('book_variation_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
