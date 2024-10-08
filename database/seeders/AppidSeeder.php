<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AppidSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $zappid = [
            'appid' => '003',
            'clientid' => 1,
            'name' => 'AG',
        ];

        DB::table('zappid')->insert($zappid);
    }
}
