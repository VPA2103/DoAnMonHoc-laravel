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
        Schema::create('buoc_nau', function (Blueprint $table) {
            $table->id('ma_buoc_nau');
            $table->foreignId('ma_cong_thuc')
                ->references('ma_cong_thuc')
                ->on('cong_thuc')
                ->cascadeOnDelete();

            $table->integer('so_thu_tu');
            $table->text('noi_dung');
            $table->string('hinh_anh')->nullable();
            $table->integer('thoi_gian')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buoc_nau');
    }
};
