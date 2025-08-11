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
       Schema::create('appointments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('pet_id')->constrained('pets')->onDelete('cascade');
        $table->foreignId('veterinarian_id')->nullable()->constrained('users')->onDelete('set null');
        $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
        $table->date('date');
        $table->time('time');
        $table->string('reason')->nullable(); // motivo
        $table->enum('status', ['pendiente', 'completada', 'cancelada'])->default('pendiente');
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
