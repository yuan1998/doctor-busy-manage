<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory, HasDateTimeFormatter;

    const STATUS_NORMAL = 0;
    const STATUS_IN_SURGERY = 1;
    const STATUS_REST = 2;

    const STATUS_MAP = [
        self::STATUS_NORMAL => '可面诊',
        self::STATUS_IN_SURGERY => '手术中',
        self::STATUS_REST => '休息',
    ];
    protected $fillable = [
        'name',
        'enable',
        'status',
        'surgery_id',
        'surgery_room',
        'start_date',
        'end_date',
    ];
    protected $casts = [
        'enable' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function surgery()
    {
        return $this->belongsTo(Surgery::class);
    }

    public function getTimeRangeAttribute()
    {
        return $this->start_date->format('H:i:s') . ' ~ ' . $this->end_date->format('H:i:s');
    }

}
