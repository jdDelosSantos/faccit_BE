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
        Schema::create('laboratories', function (Blueprint $table) {
            $table->id();
            $table->string("laboratory");
            $table->string("subject_code");
            $table->string("subject_name");
            $table->string("subject_day");
            $table->string("start_time");
            $table->string("end_time");
            $table->timestamps();

            $table->foreign('subject_code')
            ->references('subject_code')
            ->on('subjects')
            ->onDelete('cascade')
            ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laboratories');
    }
};
