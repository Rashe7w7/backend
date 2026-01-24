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
        Schema::table('estudiante_ayudante', function (Blueprint $table) {
            // Eliminar la foreign key de asignatura_id
            $table->dropForeign(['asignatura_id']);
            $table->dropColumn('asignatura_id');
            // Eliminar las columnas de fechas
            $table->dropColumn('fecha_inicio');
            $table->dropColumn('fecha_fin');
        });

        Schema::table('estudiante_ayudante', function (Blueprint $table) {
            // Agregar la foreign key a profesor_asignatura
            $table->foreignId('profesor_asignatura_id')->constrained('profesor_asignatura')->cascadeOnDelete();
            // Agregar la foreign key a curso_academico
            $table->foreignId('curso_academico_id')->constrained('curso_academico')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('estudiante_ayudante', function (Blueprint $table) {
            // Eliminar las foreign keys
            $table->dropForeign(['profesor_asignatura_id']);
            $table->dropForeign(['curso_academico_id']);
            $table->dropColumn('profesor_asignatura_id');
            $table->dropColumn('curso_academico_id');
        });

        Schema::table('estudiante_ayudante', function (Blueprint $table) {
            // Restaurar las columnas de fechas y asignatura_id
            $table->foreignId('asignatura_id')->constrained('asignatura')->cascadeOnDelete();
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_fin')->nullable();
        });
    }
};
