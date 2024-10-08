<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $client = [
            'name' => 'PT.Lasuarindo',
            'address' => 'Jl. Ahmadyani no 669 Apartement Gateway',
            'phone' => '08226000411',
            'email' => 'info@lasuarindo.co.id',
            'web' => 'lasuarindo.co.id',
        ];

        DB::table('agclients')->insert($client);
    }
}
