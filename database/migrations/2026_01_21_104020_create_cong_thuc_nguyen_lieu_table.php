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
        Schema::create('cong_thuc_nguyen_lieu', function (Blueprint $table) {
            $table->id();

            // 🔑 Khóa ngoại tới công thức
            $table->unsignedBigInteger('ma_cong_thuc');

            // 🔑 Khóa ngoại tới nguyên liệu (master)
            $table->unsignedBigInteger('ma_nguyen_lieu');

            // 🧮 ĐÃ THAY ĐỔI: Chuyển từ decimal sang string để nhận được "500g", "1 bát"...
            $table->string('so_luong')->nullable();

            // ⛓ Ràng buộc khóa ngoại
            $table->foreign('ma_cong_thuc')
                  ->references('ma_cong_thuc')
                  ->on('cong_thuc')
                  ->onDelete('cascade');

            $table->foreign('ma_nguyen_lieu')
                  ->references('ma_nguyen_lieu')
                  ->on('nguyen_lieu')
                  ->onDelete('cascade');

            // 🚫 Tránh trùng nguyên liệu trong cùng một công thức
            $table->unique(['ma_cong_thuc', 'ma_nguyen_lieu']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cong_thuc_nguyen_lieu');
    }
};