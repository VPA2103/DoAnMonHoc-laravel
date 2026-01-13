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
        Schema::create('nguoi_dung', function (Blueprint $table) {
            $table->id('ma_nguoi_dung');
            $table->string('ten_nguoi_dung');
            $table->string('email')->unique();
            $table->string('mat_khau');
            $table->string('anh_dai_dien')->nullable();
            $table->enum('vai_tro', ['admin', 'user'])->default('user');
            $table->tinyInteger('trang_thai')->default(1);
            $table->timestamp('ngay_tao')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nguoi_dung');
    }
};
