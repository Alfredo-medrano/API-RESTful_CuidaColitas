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
        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('species', ['perro', 'gato', 'ave', 'otro']);
            $table->string('breed')->nullable();
            $table->date('birth_date')->nullable();
            $table->enum('sex', ['macho','hembra','desconocido'])->default('desconocido');
            $table->string('color')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('photo_path')->nullable();  
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pets');
    }
};
