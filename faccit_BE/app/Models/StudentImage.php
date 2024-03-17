<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Student;

class StudentImage extends Model
{
    use HasFactory;

    public function student()
    {
        return $this->belongsTo(Student::class, 'faith_id', 'faith_id');
    }
}
