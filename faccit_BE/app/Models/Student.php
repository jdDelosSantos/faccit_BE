<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\StudentImage;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'faith_id',
        'std_lname',
        'std_fname',
        'std_course',
        'std_level',
        'std_section',
    ];

    public function studentImages()
    {
        return $this->hasMany(StudentImage::class, 'faith_id', 'faith_id');
    }
}
