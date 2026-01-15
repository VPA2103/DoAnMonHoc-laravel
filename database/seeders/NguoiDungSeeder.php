<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class NguoiDungSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('nguoi_dung')->insert([
            'ten_nguoi_dung' => 'Admin',
            'email' => 'admin@gmail.com',
            'mat_khau' => Hash::make('123456'),
            'anh_dai_dien' => null,
            'vai_tro' => 'admin',
            'trang_thai' => 1,
            'ngay_tao' => now(),
        ]);
        DB::table('nguoi_dung')->insert([
            'ten_nguoi_dung' => 'User',
            'email' => 'user@gmail.com',
            'mat_khau' => Hash::make('123456'),
            'anh_dai_dien' => null,
            'vai_tro' => 'user',
            'trang_thai' => 1,
            'ngay_tao' => now(),
        ]);
    }
}