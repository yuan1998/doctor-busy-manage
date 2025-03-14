<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    // 获取所有 enable 的 Doctor
    public function index()
    {
        $doctors = Doctor::with(['surgery'])->where('enable', 1)->get();
        return response()->json([
            'data' => $doctors,
            'code' => 0,
            'msg' => 'OK'
        ]);
    }

    // 修改 doctor 的 status 和 surgery_id
    public function update(Request $request, $id)
    {
        $doctor = Doctor::find($id);
        if (!$doctor) {
            return response()->json([
                'code' => 1,
                'msg' => '医生不存在'
            ]);
        }
        $status = $request->get('status');
        $surgery_id = $request->get('surgery_id');
        if ($status === Doctor::STATUS_IN_SURGERY) {
            if ($doctor->status === Doctor::STATUS_IN_SURGERY)
                return response()->json([
                    'code' => 1,
                    'msg' => '医生正在手术中，不能重复手术,请先结束手术'
                ]);
            $surgery = Doctor::find($surgery_id);
            if (!$surgery) {
                return response()->json([
                    'code' => 1,
                    'msg' => '手术项目不存在'
                ]);
            }
            $doctor->status = Doctor::STATUS_IN_SURGERY;
            $doctor->surgery_id = $surgery_id;
            $startDate = $request->get("start_date");
            $endDate = $request->get("end_date");
            if (!$startDate || !$endDate) {
                $startDate = now();
                $endDate = now()->addMinutes($surgery->surgery_time);
            }
            $doctor->start_date = $startDate;
            $doctor->end_date = $endDate;
        } else {
            $doctor->status = $status;
            $doctor->surgery_id = null;
            $doctor->start_date = null;
            $doctor->end_date = null;
        }
        $doctor->save();
        return response()->json([
            'code' => 0,
            'msg' => '修改成功'
        ]);
    }
}
