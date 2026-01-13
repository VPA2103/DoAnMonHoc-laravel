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
        Schema::create('theo_doi', function (Blueprint $table) {
            $table->id('ma_theo_doi');

            // người theo dõi
            $table->unsignedBigInteger('ma_nguoi_theo_doi');
            $table->foreign('ma_nguoi_theo_doi')
                ->references('ma_nguoi_dung')
                ->on('nguoi_dung')
                ->cascadeOnDelete();

            // người được theo dõi
            $table->unsignedBigInteger('ma_nguoi_duoc_theo_doi');
            $table->foreign('ma_nguoi_duoc_theo_doi')
                ->references('ma_nguoi_dung')
                ->on('nguoi_dung')
                ->cascadeOnDelete();

            $table->timestamps();

            // tránh theo dõi trùng
            $table->unique(['ma_nguoi_theo_doi', 'ma_nguoi_duoc_theo_doi']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('theo_doi');
    }
};
