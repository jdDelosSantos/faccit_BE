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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->string("class_code");
            $table->string("faith_id");
            $table->date("date");
            $table->time("time_in");
            $table->time("time_out");
            $table->string("status");
            $table->timestamps();

            $table->foreign('class_code')
            ->references('class_code')
            ->on('classes')
            ->onDelete('cascade')
            ->onUpdate('cascade');

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
        Schema::dropIfExists('attendances');
    }
};
