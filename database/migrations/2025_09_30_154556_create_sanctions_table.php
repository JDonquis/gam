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
        Schema::create('sanctions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id');
            $table->unsignedBigInteger('resignation_id')->nullable();
            $table->foreign('resignation_id')
                ->references('id')
                ->on('resignations')
                ->onDelete('cascade');
            $table->unsignedBigInteger('incidence_id')->nullable();
            $table->foreign('incidence_id')
                ->references('id')
                ->on('incidences')
                ->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sanctions');
    }
};
