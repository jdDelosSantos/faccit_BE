<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\StudentImage;

class Student extends Model
{
    use HasFactory;

    public function studentImages()
    {
        return $this->hasMany(StudentImage::class, 'faith_id', 'faith_id');
    }
}
