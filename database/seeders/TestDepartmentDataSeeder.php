<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestDepartmentDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Department::query()->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $data = ['整外科','皮肤科','无创科'];
        foreach ($data as $d) {
            Department::create([
                'name' => $d,
                'description' => "{$d} 医生手术动态表",
            ]);
        }
    }
}
