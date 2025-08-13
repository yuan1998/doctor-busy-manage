<?php

namespace App\Http\Controllers;

use App\Models\Surgery;
use Illuminate\Http\Request;

class SurgeryController extends Controller
{
    // 获取所有 surgery , where enable
    public function index()
    {
        $surgeries = Surgery::where('enable', 1)->get();
        return response()->json([
            'code' => 0,
            'data' => $surgeries,
            'msg' => 'OK'
        ]);
    }
}
