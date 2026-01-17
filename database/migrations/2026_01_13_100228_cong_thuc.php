<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('cong_thuc', function (Blueprint $table) {
            $table->id('ma_cong_thuc');

            // FK -> nguoi_dung
            $table->unsignedBigInteger('ma_nguoi_dung');
            $table->foreign('ma_nguoi_dung')
                ->references('ma_nguoi_dung')
                ->on('nguoi_dung')
                ->cascadeOnDelete();

            // FK -> danh_muc
            $table->unsignedBigInteger('ma_danh_muc');
            $table->foreign('ma_danh_muc')
                ->references('ma_danh_muc')
                ->on('danh_muc');

            $table->string('ten_cong_thuc');
            $table->string('slug')->unique();

            // ✅ ẢNH CÔNG THỨC (1 ẢNH LỚN)
            $table->string('anh_cong_thuc')->nullable();

            $table->text('mo_ta')->nullable();
            $table->enum('do_kho', ['De', 'Trung binh', 'Kho'])->nullable();
            $table->integer('thoi_gian_nau')->nullable();
            $table->tinyInteger('trang_thai')->default(1);
            $table->timestamp('ngay_tao')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cong_thuc');
    }
};
