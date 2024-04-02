<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Classes;
use App\Models\Student;

class ClassStudents extends Model
{
    use HasFactory;

    public function students()
    {
        return $this->belongsTo(Classes::class, 'class_code', 'class_code');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'faith_id', 'faith_id');
    }
}
