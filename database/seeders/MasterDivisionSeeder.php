<?php

namespace Database\Seeders;

use App\Models\MasterDivision;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterDivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        MasterDivision::create(['name' => 'All',                    'created_by' => 'seed']);
        MasterDivision::create(['name' => 'Application Support',    'created_by' => 'seed']);
        MasterDivision::create(['name' => 'Technical Support',      'created_by' => 'seed']);
        MasterDivision::create(['name' => 'Infrastructure',         'created_by' => 'seed']);
        MasterDivision::create(['name' => 'Project Development',    'created_by' => 'seed']);
    }
}
