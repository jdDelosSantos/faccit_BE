<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Classes;

class Facility extends Model
{
    use HasFactory;

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_code', 'class_code');
    }

    public function classes()
    {
        return $this->belongsTo(Classes::class, 'class_code', 'class_code');
    }
}
