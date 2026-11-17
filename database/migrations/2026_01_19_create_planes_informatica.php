<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $carrera = DB::table('carrera')->where('nombre', 'Ingeniería Informática')->first();

        if (! $carrera) {
            $facultad = DB::table('facultad')->first();
            if (! $facultad) {
                return;
            }

            $carreraId = DB::table('carrera')->insertGetId([
                'nombre' => 'Ingeniería Informática',
                'facultad_id' => $facultad->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $carreraId = $carrera->id;
        }

        // Crear 4 planes de estudio con nombres a, b, c, d
        $nombresPlanes = ['a', 'b', 'c', 'd'];
        $planes = [];

        foreach ($nombresPlanes as $index => $nombre) {
            // Verificar si el plan ya existe
            $planExistente = DB::table('plan_estudio')
                ->where('carrera_id', $carreraId)
                ->where('nombre', $nombre)
                ->first();

            if (! $planExistente) {
                $planId = DB::table('plan_estudio')->insertGetId([
                    'nombre' => $nombre,
                    'carrera_id' => $carreraId,
                    'fecha_inicio' => (2020 + $index).'-09-01',
                    'fecha_fin' => null,
                    'vigente' => $index == 3,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $planes[$nombre] = $planId;
            } else {
                $planes[$nombre] = $planExistente->id;
            }
        }

        // Asociar los planes a la carrera en carrera_plan
        foreach ($planes as $nombre => $planId) {
            DB::table('carrera_plan')->insertOrIgnore([
                'carrera_id' => $carreraId,
                'plan_estudio_id' => $planId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // No hacer nada en el rollback para no perder datos
    }
};
