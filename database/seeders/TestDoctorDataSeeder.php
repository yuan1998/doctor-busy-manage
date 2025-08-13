<?php

namespace Database\Seeders;

use App\Models\Doctor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestDoctorDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            '整外科' => [
                "王仁根",
                "李小正",
                "张沙沙",
                "王逸涛",
            ],
            '皮肤科' => [
                "李静",
                "王师平",
                "张丹",
                "史苗苗",
                "赵晓彦",
                "牛正涛",
                "范倩茜",
                "吕姣姣",
                "李祥伟",
                "于丽源",
            ],
            '无创科' => [
                "王小明",
                "李大海",
                "张三丰",
                "赵四",
            ]
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Doctor::query()->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        foreach ($data as $departmentName => $doctors) {
            $department = \App\Models\Department::query()->where('name', $departmentName)->first();
            if (!$department) {
                continue;
            }
            foreach ($doctors as $doctorName) {
                Doctor::query()->create([
                    'name' => $doctorName,
                    'enable' => true,
                    'status' => Doctor::STATUS_NORMAL,
                    'department_id' => $department->id,
                ]);
            }
        }
    }
}
