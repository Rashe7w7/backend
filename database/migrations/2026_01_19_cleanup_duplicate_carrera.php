<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Encontrar duplicados de Ingeniería Informática
        $duplicados = DB::table('carrera')
            ->where('nombre', 'Ingeniería Informática')
            ->get();

        if (count($duplicados) > 1) {
            // Mantener el primero y eliminar el segundo
            $primero = $duplicados->first();
            $segundo = $duplicados->skip(1)->first();

            // Reasignar los planes del segundo al primero
            DB::table('plan_estudio')
                ->where('carrera_id', $segundo->id)
                ->update(['carrera_id' => $primero->id]);

            // Reasignar las asociaciones del segundo al primero
            DB::table('carrera_plan')
                ->where('carrera_id', $segundo->id)
                ->update(['carrera_id' => $primero->id]);

            // Eliminar el duplicado
            DB::table('carrera')->where('id', $segundo->id)->delete();
        }
    }

    public function down(): void
    {
        // No hacer nada
    }
};
