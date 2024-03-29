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
        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->string("laboratory");
            $table->string("class_code");
            $table->string("class_name");
            $table->string("class_day");
            $table->string("start_time");
            $table->string("end_time");
            $table->timestamps();

            $table->foreign('class_code')
            ->references('class_code')
            ->on('classes')
            ->onDelete('cascade')
            ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facilities');
    }
};




