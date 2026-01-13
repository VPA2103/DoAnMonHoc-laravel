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
        Schema::create('ke_hoach_bua_an', function (Blueprint $table) {
            $table->id('ma_ke_hoach');

            $table->unsignedBigInteger('ma_nguoi_dung');
            $table->foreign('ma_nguoi_dung')
                ->references('ma_nguoi_dung')
                ->on('nguoi_dung')
                ->cascadeOnDelete();

            $table->date('ngay');
            $table->string('ten_bua_an'); // sáng / trưa / tối
            $table->text('ghi_chu')->nullable();

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ke_hoach_bua_an');
    }
};
