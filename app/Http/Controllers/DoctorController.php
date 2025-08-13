<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Doctor;
use App\Models\Surgery;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    // 获取所有 enable 的 Doctor
    public function index(Request $request)
    {
        $dId = $request->get('department_id');
        $doctorId = $request->get('doctor_id');
        $department = null;
        $doctorsQuery = Doctor::with(['surgery'])
            ->where('enable', 1);

        if ($dId) {
            $doctorsQuery->where('department_id', $dId);
            $department = Department::query()->where('id', $dId)->first();
        }
        if ($doctorId) {
            $doctorsQuery->where('id', $doctorId);
        }

        $doctors = $doctorsQuery->get();
        return response()->json([
            'data' => [
                'doctors' => $doctors,
                'department' => $department,
            ],
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
            $doctor->status = Doctor::STATUS_IN_SURGERY;
            $surgery = Surgery::find($surgery_id);
            if ($surgery) {
                $doctor->surgery_room = $request->get("surgery_room");
                $doctor->surgery_id = $surgery_id;
                $startDate = $request->get("start_date");
                $endDate = $request->get("end_date");
                if (!$startDate || !$endDate) {
                    $startDate = now();
                    $endDate = now()->addMinutes($surgery->surgery_time);
                }
                $doctor->start_date = $startDate;
                $doctor->end_date = $endDate;
            }
        } else {
            $doctor->status = $status;
            $doctor->surgery_id = null;
            $doctor->surgery_room = null;
            $doctor->start_date = null;
            $doctor->end_date = null;
        }
        $doctor->save();
        return response()->json([
            'code' => 0,
            'msg' => '修改成功'
        ]);
    }

    public function delaySurgeryEndTime(Request $request, $id)
    {
        $doctor = Doctor::find($id);
        if (!$doctor) {
            return response()->json([
                'code' => 1,
                'msg' => '医生不存在'
            ]);
        }
        if ($doctor->status !== Doctor::STATUS_IN_SURGERY)
            return response()->json([
                'code' => 1,
                'msg' => '医生不在手术中'
            ]);
        $endDate = $request->get("end_date");
        if (!$endDate) {
            return response()->json([
                'code' => 1,
                'msg' => '请指定手术结束时间'
            ]);
        }
        $doctor->end_date = $endDate;
        $doctor->save();
        return response()->json([
            'code' => 0,
            'msg' => '修改手术结束时间成功'
        ]);
    }
}
