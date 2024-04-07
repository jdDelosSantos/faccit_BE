<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImageController extends Controller
{
    public function getAllImages()
    {
        $studentImages = DB::table('student_images')
            ->select(
                'faith_id',
                'std_folder_url',
                'std_folder_img_url',
            )
            ->union($this->getProfessorImagesSubquery());

        $images = $studentImages->get();

        return response()->json($images);
    }

    private function getProfessorImagesSubquery()
    {
        return DB::table('professor_images')
            ->select(
                'faith_id',
                'std_folder_url',
                'std_folder_img_url',
            );
    }
}
