<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $tables = [
            'indicador_carrera',
            'indicador_grupo',
            'indicador_departamento',
            'indicador_facultad',
            'indicador_anio',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'nombre')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->string('nombre')->after('id');
                    $t->index('nombre');
                });
            }
        }
    }

    public function down(): void
    {
        $tables = [
            'indicador_carrera',
            'indicador_grupo',
            'indicador_departamento',
            'indicador_facultad',
            'indicador_anio',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'nombre')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->dropIndex(['nombre']);
                    $t->dropColumn('nombre');
                });
            }
        }
    }
};
