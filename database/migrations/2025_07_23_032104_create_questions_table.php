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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained()->onDelete('cascade');
            $table->string('title')->nullable(); // optional title
            $table->text('content'); // nội dung đề bài (dùng CKEditor)
            $table->text('solution')->nullable(); // bài giải (có thể để trống, dùng CKEditor)
            $table->string('answer')->nullable(); // đáp án đúng
            $table->enum('type', ['multiple_choice', 'true_false', 'fill_blank']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
