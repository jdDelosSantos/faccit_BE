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
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string("subject_code")->unique();
            $table->string("subject_name");
            $table->string("subject_description");
            $table->string("prof_id")->nullable();
            $table->string("subject_day")->nullable();
            $table->time("start_time")->nullable();
            $table->time("end_time")->nullable();
            $table->string("subject_status")->default('Active');
            $table->timestamps();

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
        Schema::dropIfExists('subjects');
    }
};
