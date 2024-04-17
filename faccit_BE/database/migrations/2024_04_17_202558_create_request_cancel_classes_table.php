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
        Schema::create('request_cancel_classes', function (Blueprint $table) {
            $table->id();
            $table->string("prof_id");
            $table->string("laboratory");
            $table->string("class_code");
            $table->string("class_day");
            $table->time("start_time");
            $table->time("end_time");
            $table->string("cancel_class_status")->default("Pending");
            $table->timestamps();

            $table->foreign('prof_id')
            ->references('prof_id')
            ->on('users')
            ->onDelete('cascade')
            ->onUpdate('cascade');

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
        Schema::dropIfExists('request_cancel_classes');
    }
};
