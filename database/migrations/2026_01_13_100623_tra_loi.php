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
        Schema::create('tra_loi', function (Blueprint $table) {
            $table->id('ma_tra_loi');

            $table->unsignedBigInteger('ma_cau_hoi');
            $table->foreign('ma_cau_hoi')
                ->references('ma_cau_hoi')
                ->on('cau_hoi')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('ma_nguoi_dung');
            $table->foreign('ma_nguoi_dung')
                ->references('ma_nguoi_dung')
                ->on('nguoi_dung')
                ->cascadeOnDelete();

            $table->text('noi_dung');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tra_loi');
    }
};
