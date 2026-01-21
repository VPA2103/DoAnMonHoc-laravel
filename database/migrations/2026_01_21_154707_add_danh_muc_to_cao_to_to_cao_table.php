<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('to_cao', function (Blueprint $table) {
            $table->string('danh_muc_to_cao')->after('noi_dung');
        });
    }

    public function down(): void
    {
        Schema::table('to_cao', function (Blueprint $table) {
            $table->dropColumn('danh_muc_to_cao');
        });
    }
};

