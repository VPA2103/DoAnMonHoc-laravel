<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Chạy khi php artisan migrate
     */
    public function up(): void
    {
        Schema::table('cau_hoi', function (Blueprint $table) {
            // Xóa cột không dùng
            $table->dropColumn('tieu_de');
            $table->dropColumn('trang_thai');
        });
    }

    /**
     * Reverse the migrations.
     * Chạy khi php artisan migrate:rollback
     */
    public function down(): void
    {
        Schema::table('cau_hoi', function (Blueprint $table) {
            // Tạo lại cột nếu rollback
            $table->string('tieu_de');
            $table->tinyInteger('trang_thai')->default(1);
        });
    }
};
