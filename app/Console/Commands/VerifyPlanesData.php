<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class VerifyPlanesData extends Command
{
    protected $signature = 'planes:verify';
    protected $description = 'Verifica los datos de planes de estudio y carreras';

    public function handle()
    {
        $this->info('=== VERIFICACIÓN DE DATOS ===\n');

        // Verificar carreras
        $carreras = DB::table('carrera')->get();
        $this->info("Total de carreras: " . count($carreras));
        foreach ($carreras as $carrera) {
            $this->line("  - {$carrera->nombre} (ID: {$carrera->id})");
        }

        // Verificar planes de estudio
        $planes = DB::table('plan_estudio')->get();
        $this->info("\nTotal de planes de estudio: " . count($planes));
        foreach ($planes as $plan) {
            $carrera = DB::table('carrera')->find($plan->carrera_id);
            $this->line("  - Plan '{$plan->nombre}' (ID: {$plan->id}) para {$carrera->nombre}");
        }

        // Verificar asociaciones carrera_plan
        $asociaciones = DB::table('carrera_plan')
            ->join('carrera', 'carrera_plan.carrera_id', '=', 'carrera.id')
            ->join('plan_estudio', 'carrera_plan.plan_estudio_id', '=', 'plan_estudio.id')
            ->select('carrera.nombre as carrera', 'plan_estudio.nombre as plan')
            ->get();
        
        $this->info("\nTotal de asociaciones carrera_plan: " . count($asociaciones));
        foreach ($asociaciones as $asoc) {
            $this->line("  - Carrera: {$asoc->carrera} → Plan: {$asoc->plan}");
        }

        return 0;
    }
}
