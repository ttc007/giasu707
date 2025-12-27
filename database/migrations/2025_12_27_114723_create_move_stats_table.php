<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('move_stats', function (Blueprint $table) {
            $table->id();
            // Đổi từ text sang string và cấp độ dài cụ thể (ví dụ 500 ký tự là dư dùng cho bàn cờ)
            $table->string('board', 500); 
            $table->string('turn', 10);
            $table->string('move_text', 50);
            $table->integer('win_count')->default(0);
            $table->integer('lose_count')->default(0);
            $table->integer('draw_count')->default(0);
            
            $table->timestamps();
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('move_stats');
    }
};