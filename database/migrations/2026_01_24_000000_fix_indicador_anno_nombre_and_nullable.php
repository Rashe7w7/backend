<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Alinea indicador_anno con el resto de tablas indicador_*:
 *  - columna `nombre` (2026_01_17_200000 la omitía por el typo 'indicador_anio'),
 *  - anio_academico_id y curso_academico_id nullable (2026_01_15_make_indicador_relations_nullable
 *    no incluía esta tabla, e IndicadorController::store inserta anio_academico_id = null).
 * Idempotente: en bases nuevas `nombre` ya existe y solo se aplica el cambio a nullable.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('indicador_anno')) {
            return;
        }

        if (! Schema::hasColumn('indicador_anno', 'nombre')) {
            // Se agrega nullable, se rellena con el nombre del indicador y luego se hace NOT NULL,
            // para no fallar en bases que ya tienen filas.
            Schema::table('indicador_anno', function (Blueprint $table) {
                $table->string('nombre')->nullable()->after('id');
                $table->index('nombre');
            });

            $nombres = DB::table('indicador')->pluck('nombre', 'id');
            DB::table('indicador_anno')->orderBy('id')->get(['id', 'indicador_id'])
                ->each(function ($fila) use ($nombres) {
                    DB::table('indicador_anno')->where('id', $fila->id)
                        ->update(['nombre' => $nombres[$fila->indicador_id] ?? '']);
                });

            Schema::table('indicador_anno', function (Blueprint $table) {
                $table->string('nombre')->nullable(false)->change();
            });
        }

        Schema::table('indicador_anno', function (Blueprint $table) {
            $table->foreignId('anio_academico_id')->nullable()->change();
            $table->foreignId('curso_academico_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        // No se revierte: volver a NOT NULL fallaría con filas que tengan NULL,
        // y `nombre` puede venir de 2026_01_17_200000 en bases nuevas.
    }
};
