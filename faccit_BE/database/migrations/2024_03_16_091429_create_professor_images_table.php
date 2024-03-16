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
        Schema::create('professor_images', function (Blueprint $table) {
            $table->id();
            $table->string('prof_id');
            $table->string('std_folder_url');
            $table->string('std_folder_img_url');
            $table->timestamps();

            //Foreign Key Reference to the Student
            $table->foreign('prof_id')
            ->references('prof_id')
            ->on('users')
            ->onDelete('cascade')
            ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professor_images');
    }
};
