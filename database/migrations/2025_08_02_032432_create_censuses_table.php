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
        Schema::create('censuses', function (Blueprint $table) {
            $table->id();
            $table->string('file');
            $table->string('title');
            $table->string('size');
            $table->string('type');
            $table->foreignId('user_id');
            $table->integer('percentage')->default(0);
            $table->boolean('is_completed')->default(false);
            $table->foreignId('configuration_id');
            $table->integer('type_document_id');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('censuses');
    }
};
