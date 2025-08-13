<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Surgery extends Model
{
    use HasFactory,HasDateTimeFormatter;

    public function department() {
        return $this->belongsTo(Department::class);
    }
}
