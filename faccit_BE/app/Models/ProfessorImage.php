<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class ProfessorImage extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class, 'prof_id', 'prof_id');
    }
}
