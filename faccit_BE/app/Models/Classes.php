<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ClassStudents;

class Classes extends Model
{
    use HasFactory;

    public function classStudents()
    {
        return $this->hasMany(ClassStudents::class, 'class_code', 'class_code');
    }
}
