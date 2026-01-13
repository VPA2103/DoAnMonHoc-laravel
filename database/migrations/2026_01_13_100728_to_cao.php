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
        Schema::create('to_cao', function (Blueprint $table) {
            $table->id('ma_to_cao');

            // FK tới nguoi_dung
            $table->unsignedBigInteger('ma_nguoi_dung');
            $table->foreign('ma_nguoi_dung')
                ->references('ma_nguoi_dung')
                ->on('nguoi_dung')
                ->cascadeOnDelete();

            $table->text('noi_dung');
            $table->enum('trang_thai', ['ChoDuyet', 'DaXuLy'])->default('ChoDuyet');

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('to_cao');
    }
};
