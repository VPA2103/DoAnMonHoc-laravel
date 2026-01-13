<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('danh_gia', function (Blueprint $table) {
            $table->id('ma_danh_gia');

            $table->unsignedBigInteger('ma_nguoi_dung');
            $table->foreign('ma_nguoi_dung')
                ->references('ma_nguoi_dung')
                ->on('nguoi_dung')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('ma_cong_thuc');
            $table->foreign('ma_cong_thuc')
                ->references('ma_cong_thuc')
                ->on('cong_thuc')
                ->cascadeOnDelete();

            $table->tinyInteger('so_sao'); // 1–5
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('danh_gia');
    }
};
