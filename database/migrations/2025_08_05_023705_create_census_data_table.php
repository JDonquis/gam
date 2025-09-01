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
        Schema::create('census_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('census_id');
            $table->json('data');
            $table->longText('observation')->nullable();
            $table->boolean('is_foreign')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('census_data');
    }
};
