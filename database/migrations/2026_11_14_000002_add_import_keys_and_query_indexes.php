<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('estudiante', function (Blueprint $table) {
            $table->string('identificador_institucional')->nullable()->unique()->after('id');
            $table->index(['grupo_id', 'carrera']);
        });

        foreach ([
            'indicador_carrera' => 'carrera_id',
            'indicador_grupo' => 'grupo_id',
            'indicador_departamento' => 'departamento_id',
            'indicador_anno' => 'anio_academico_id',
        ] as $tableName => $entityColumn) {
            Schema::table($tableName, function (Blueprint $table) use ($entityColumn) {
                $table->index([$entityColumn, 'curso_academico_id']);
            });
        }
    }

    public function down(): void
    {
        foreach ([
            'indicador_carrera' => 'carrera_id',
            'indicador_grupo' => 'grupo_id',
            'indicador_departamento' => 'departamento_id',
            'indicador_anno' => 'anio_academico_id',
        ] as $tableName => $entityColumn) {
            Schema::table($tableName, fn (Blueprint $table) => $table->dropIndex([$entityColumn, 'curso_academico_id']));
        }

        Schema::table('estudiante', function (Blueprint $table) {
            $table->dropIndex(['grupo_id', 'carrera']);
            $table->dropUnique(['identificador_institucional']);
            $table->dropColumn('identificador_institucional');
        });
    }
};
