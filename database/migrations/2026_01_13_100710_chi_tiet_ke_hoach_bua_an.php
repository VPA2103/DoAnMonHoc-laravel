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
        Schema::create('chi_tiet_ke_hoach_bua_an', function (Blueprint $table) {
            $table->id('ma_chi_tiet');

            // FK tới ke_hoach_bua_an
            $table->unsignedBigInteger('ma_ke_hoach');
            $table->foreign('ma_ke_hoach')
                ->references('ma_ke_hoach')
                ->on('ke_hoach_bua_an')
                ->cascadeOnDelete();

            // FK tới cong_thuc
            $table->unsignedBigInteger('ma_cong_thuc');
            $table->foreign('ma_cong_thuc')
                ->references('ma_cong_thuc')
                ->on('cong_thuc')
                ->cascadeOnDelete();

            $table->date('ngay_an');
            $table->enum('bua_an', ['Sang', 'Trua', 'Toi', 'Phu']);

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chi_tiet_ke_hoach_bua_an');
    }
};
