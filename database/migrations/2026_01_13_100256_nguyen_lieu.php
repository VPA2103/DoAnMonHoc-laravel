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
        Schema::create('nguyen_lieu', function (Blueprint $table) {
            $table->id('ma_nguyen_lieu');
            $table->foreignId('ma_cong_thuc')
                ->references('ma_cong_thuc')
                ->on('cong_thuc')
                ->cascadeOnDelete();

            $table->string('ten_nguyen_lieu');
            $table->string('don_vi_tinh')->nullable();
            $table->decimal('so_luong', 10, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nguyen_lieu');
    }
};
