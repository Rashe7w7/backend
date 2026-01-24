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
        // Corregir la restricción de clave foránea en indicador_grupo
        Schema::table('indicador_grupo', function (Blueprint $table) {
            $table->dropForeign(['curso_academico_id']);
            $table->foreign('curso_academico_id')
                ->references('id')
                ->on('curso_academico')
                ->cascadeOnDelete();
        });

        // Corregir la restricción de clave foránea en indicador_carrera
        Schema::table('indicador_carrera', function (Blueprint $table) {
            $table->dropForeign(['curso_academico_id']);
            $table->foreign('curso_academico_id')
                ->references('id')
                ->on('curso_academico')
                ->cascadeOnDelete();
        });

        // Corregir la restricción de clave foránea en indicador_departamento
        Schema::table('indicador_departamento', function (Blueprint $table) {
            $table->dropForeign(['curso_academico_id']);
            $table->foreign('curso_academico_id')
                ->references('id')
                ->on('curso_academico')
                ->cascadeOnDelete();
        });

        // Corregir la restricción de clave foránea en indicador_anno
        // Este tiene dos FKs de curso_academico_id, uno debe ser anio_academico_id
        Schema::table('indicador_anno', function (Blueprint $table) {
            $table->dropForeign(['curso_academico_id']);
            $table->foreign('curso_academico_id')
                ->references('id')
                ->on('curso_academico')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Esta migración no se puede revertir porque arregla relaciones mal configuradas
        // No hacer nada en down
    }
};
