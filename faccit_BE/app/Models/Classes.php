<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ClassStudents;
use App\Models\Facility;

class Classes extends Model
{
    use HasFactory;

    public function classStudents()
    {
        return $this->hasMany(ClassStudents::class, 'class_code', 'class_code');
    }

    public function facilities()
    {
        return $this->hasMany(Facility::class, 'class_code', 'class_code');
    }
}
