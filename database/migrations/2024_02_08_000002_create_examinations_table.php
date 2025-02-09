<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('examinations', function (Blueprint $table) {
            $table->id();
            $table->string('patient_name');
            $table->text('patient_address');
            $table->datetime('examination_time');
            $table->decimal('height', 5, 2);
            $table->decimal('weight', 5, 2);
            $table->integer('systole');
            $table->integer('diastole');
            $table->integer('heart_rate');
            $table->integer('respiration_rate');
            $table->decimal('temperature', 4, 1);
            $table->text('examination_result');
            $table->string('examination_file')->nullable();
            $table->foreignId('doctor_id')->constrained('users');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('examinations');
    }
};
