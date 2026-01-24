<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Carrera;
use App\Models\PlanEstudio;

class UpdatePlanesEstudio extends Command
{
    protected $signature = 'planes:update-informatica';
    protected $description = 'Actualiza los planes de estudio de Informática con nombres a, b, c, d';

    public function handle()
    {
        // Encontrar la carrera de Informática
        $carrera = Carrera::where('nombre', 'like', '%Informatica%')->first();
        
        if (!$carrera) {
            $this->error('No se encontró la carrera Informática');
            return 1;
        }

        $this->info("Carrera encontrada: {$carrera->nombre} (ID: {$carrera->id})");

        // Obtener o crear 4 planes de estudio para la carrera
        $nombresPlanes = ['a', 'b', 'c', 'd'];
        $planes = [];

        foreach ($nombresPlanes as $index => $nombre) {
            $plan = PlanEstudio::where('carrera_id', $carrera->id)
                ->where('nombre', $nombre)
                ->first();

            if (!$plan) {
                // Crear nuevo plan
                $plan = PlanEstudio::create([
                    'nombre' => $nombre,
                    'carrera_id' => $carrera->id,
                    'fecha_inicio' => (2020 + $index) . '-09-01',
                    'fecha_fin' => null,
                    'vigente' => $index == 3, // El último es vigente
                ]);
                $this->info("Plan '$nombre' creado (ID: {$plan->id})");
            } else {
                $this->info("Plan '$nombre' ya existe (ID: {$plan->id})");
            }

            $planes[$nombre] = $plan->id;
        }

        // Asociar todos los planes a la carrera en carrera_plan
        foreach ($planes as $nombre => $planId) {
            $exists = DB::table('carrera_plan')
                ->where('carrera_id', $carrera->id)
                ->where('plan_estudio_id', $planId)
                ->exists();

            if (!$exists) {
                DB::table('carrera_plan')->insert([
                    'carrera_id' => $carrera->id,
                    'plan_estudio_id' => $planId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $this->info("Asociación carrera_plan creada para plan '$nombre'");
            } else {
                $this->info("Plan '$nombre' ya está asociado a la carrera");
            }
        }

        // Verificar resultados
        $planesAsociados = DB::table('carrera_plan')
            ->where('carrera_id', $carrera->id)
            ->join('plan_estudio', 'carrera_plan.plan_estudio_id', '=', 'plan_estudio.id')
            ->select('plan_estudio.nombre', 'plan_estudio.id')
            ->get();

        $this->info("\n✓ Planes de estudio asociados a {$carrera->nombre}:");
        foreach ($planesAsociados as $plan) {
            $this->line("  - {$plan->nombre} (ID: {$plan->id})");
        }

        $this->info("\n¡Actualización completada!");
        return 0;
    }
}
