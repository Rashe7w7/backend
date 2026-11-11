<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Restaura la migración que crea `anio_asignatura` (App\Models\AnioAsignatura).
 * El nombre coincide con el registro que 2026_01_19_populate_anio_asignatura
 * inserta a mano en `migrations`, así que en bases existentes ya figura como
 * ejecutada y no se vuelve a correr.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('anio_asignatura')) {
            return;
        }

        Schema::create('anio_asignatura', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anio_academico_id')->constrained('anio_academico')->cascadeOnDelete();
            $table->foreignId('asignatura_id')->constrained('asignatura')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['anio_academico_id', 'asignatura_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anio_asignatura');
    }
};
