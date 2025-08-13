<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestDepartmentDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Department::query()->truncate();
        $data = ['整外科','皮肤科','无创科'];
        foreach ($data as $d) {
            Department::create([
                'name' => $d,
                'description' => "{$d} 医生手术动态表",
            ]);
        }
    }
}
