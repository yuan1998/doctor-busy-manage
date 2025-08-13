<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Surgery;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestSurgeryDataSeeder extends Seeder
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
                [
                    'name' => '重睑 / 外切眼袋 手术',
                    'enable' => 1,
                    'surgery_time' => 60,

                ],
                [
                    'name' => '隐痕眼袋',
                    'enable' => 1,
                    'surgery_time' => 60,

                ],
                [
                    'name' => '提眉',
                    'enable' => 1,
                    'surgery_time' => 60,

                ],
                [
                    'name' => '假体隆胸',
                    'enable' => 1,
                    'surgery_time' => 180,

                ],
                [
                    'name' => '胸悬吊',
                    'enable' => 1,
                    'surgery_time' => 360,

                ],
                [
                    'name' => '单部位吸脂（腰腹/ 大腿/ 双上臂）',
                    'enable' => 1,
                    'surgery_time' => 150,

                ],
                [
                    'name' => '上半身吸脂+祛皮',
                    'enable' => 1,
                    'surgery_time' => 240,

                ],
                [
                    'name' => '面吸',
                    'enable' => 1,
                    'surgery_time' => 90,

                ],
                [
                    'name' => '耳软骨隆鼻',
                    'enable' => 1,
                    'surgery_time' => 180,

                ],
                [
                    'name' => '下半身吸脂',
                    'enable' => 1,
                    'surgery_time' => 180,

                ],
                [
                    'name' => '肋软骨隆鼻',
                    'enable' => 1,
                    'surgery_time' => 240,

                ],
                [
                    'name' => '拉皮',
                    'enable' => 1,
                    'surgery_time' => 240,

                ],
                [
                    'name' => '眼部手术（沙院）',
                    'enable' => 1,
                    'surgery_time' => 60,

                ],
                [
                    'name' => '面填',
                    'enable' => 1,
                    'surgery_time' => 60,

                ],
                [
                    'name' => '上半身吸脂',
                    'enable' => 1,
                    'surgery_time' => 180,

                ],
            ],
            '皮肤科' => [
                [
                    'name' => '皮肤肿瘤切除术',
                    'enable' => 1,
                    'surgery_time' => 30,

                ],
                [
                    'name' => '痣切除术',
                    'enable' => 1,
                    'surgery_time' => 30,

                ],
                [
                    'name' => '皮肤病理检查',
                    'enable' => 1,
                    'surgery_time' => 15,

                ],
                [
                    'name' => '激光治疗',
                    'enable' => 1,
                    'surgery_time' => 20,

                ],
            ],
            '无创科' => [
                [
                    'name' => '无创科手术',
                    'enable' => 1,
                    'surgery_time' => 30,
                ]
            ]
        ];
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Surgery::query()->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        foreach ($data as $key => $item) {
            $department = Department::query()->where('name', $key)->first();
            foreach ($item as $surgery) {
                Surgery::create(array_merge($surgery, [
                    'department_id' => $department->id,
                ]));
            }
        }
    }
}
