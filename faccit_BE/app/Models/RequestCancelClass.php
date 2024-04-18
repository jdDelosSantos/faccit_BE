<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Classes;
use App\Models\User;

class RequestCancelClass extends Model
{
    use HasFactory;

    public function class ()
    {
        return $this->belongsTo(Classes::class, 'class_code', 'class_code');
    }

    public function professor ()
    {
        return $this->belongsTo(User::class, 'prof_id', 'prof_id');
    }

    public function absentFacility()
    {
    return $this->hasOne(Facility::class, 'class_code', 'absent_class_code')
        ->where('class_day', 'absent_class_day')
        ->where('start_time', 'absent_start_time')
        ->where('end_time', 'absent_end_time')
        ->where('laboratory', 'absent_laboratory');
    }

    public function newFacility()
    {
    return $this->hasOne(Facility::class, 'class_code', 'class_code')
        ->where('class_day', 'class_day');
    }
}
