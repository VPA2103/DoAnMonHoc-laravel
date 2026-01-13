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
        Schema::create('binh_luan', function (Blueprint $table) {
            $table->id('ma_binh_luan');

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

            $table->text('noi_dung');
            $table->tinyInteger('trang_thai')->default(1);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('binh_luan');
    }
};
