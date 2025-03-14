<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InitSurgeryDataBase extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $str = "眼睛、眼袋、提眉、胸假体、胸悬吊、单部位吸脂、吸脂综合、面吸、隆鼻、其他";
        $arr = explode("、", $str);
        for ($i = 0; $i < count($arr); $i++) {
            \App\Models\Surgery::updateOrCreate([
                'name' => $arr[$i],
            ],[
                'surgery_time' => 30,
            ]);
        }

        $str = "王仁根,张沙沙,李小正,王逸涛";
        $arr = explode(",", $str);
        for ($i = 0; $i < count($arr); $i++) {
            \App\Models\Doctor::updateOrCreate([
                'name' => $arr[$i],
            ],[
                'status' => \App\Models\Doctor::STATUS_NORMAL,
                'enable' => 1,
            ]);
        }


    }
}
