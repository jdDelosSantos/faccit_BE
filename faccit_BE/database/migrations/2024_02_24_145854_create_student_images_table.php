<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('student_images', function (Blueprint $table) {
            $table->id();
            $table->string('faith_id');
            $table->string('std_folder_url');
            $table->string('std_folder_img_url');
            $table->timestamps();

            //Foreign Key Reference to the Student
            $table->foreign('faith_id')
            ->references('faith_id')
            ->on('students')
            ->onDelete('cascade')
            ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_images');
    }
};
