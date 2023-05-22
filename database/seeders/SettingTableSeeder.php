<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('setting')->insert([
            'id_setting' => 1,
            'nama_perusahaan' => 'Balet Computer',
            'alamat' => 'Jalan Prof. Moh. Yamin No 57 Kudaile Slawi Kab. Tegal',
            'telepon' => '0283-492086',
            'tipe_nota' => 1, // kecil
            'diskon' => 5,
            'path_logo' => '/img/logo.png',
            'path_kartu_member' => '/img/member.png',
        ]);
    }
}
