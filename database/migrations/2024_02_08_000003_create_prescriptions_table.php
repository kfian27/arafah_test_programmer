<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('examination_id')->constrained();
            $table->enum('status', ['pending', 'processed', 'completed'])->default('pending');
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->datetime('processed_at')->nullable();
            $table->foreignId('pharmacist_id')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('prescriptions');
    }
};
