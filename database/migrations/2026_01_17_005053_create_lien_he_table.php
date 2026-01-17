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
        Schema::create('lien_he', function (Blueprint $table) {
            $table->id('ma_lien_he');

            $table->unsignedBigInteger('ma_nguoi_dung')->nullable(); // 👈 không bắt buộc

            $table->string('ho_ten');
            $table->string('email');
            $table->string('tieu_de');
            $table->text('noi_dung');

            $table->enum('trang_thai', ['moi', 'da_xem', 'da_phan_hoi'])->default('moi'); 
            $table->timestamp('ngay_gui')->useCurrent();

            $table->foreign('ma_nguoi_dung')
                ->references('ma_nguoi_dung')
                ->on('nguoi_dung')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lien_he');
    }
};
