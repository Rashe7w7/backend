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
        // Profesor - Asignatura (relación muchos a muchos)
        Schema::create('profesor_asignatura', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profesor_id')->constrained('profesor')->cascadeOnDelete();
            $table->foreignId('asignatura_id')->constrained('asignatura')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['profesor_id', 'asignatura_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profesor_asignatura');
    }
};
