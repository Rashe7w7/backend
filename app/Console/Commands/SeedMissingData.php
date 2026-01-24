<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class SeedMissingData extends Command
{
    protected $signature = 'db:seed-missing';
    protected $description = 'Inserta disciplinas, asignaturas y relaciones profesor-asignatura faltantes';

    public function handle()
    {
        $faker = Faker::create('es_ES');
        $this->info("=== SEMBRANDO DATOS FALTANTES ===\n");

        // 1. Crear disciplinas
        $planes = DB::table('planes_estudio')->get();
        $this->info("Creando disciplinas para " . count($planes) . " planes...");
        
        $disciplinas = [];
        foreach ($planes as $plan) {
            for ($i = 1; $i <= 4; $i++) {
                $id = DB::table('disciplinas')->insertGetId([
                    'nombre' => "Disciplina " . chr(64 + $i),
                    'plan_estudio_id' => $plan->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $disciplinas[] = $id;
            }
        }
        $this->line("   ✓ Creadas " . count($disciplinas) . " disciplinas");

        // 2. Crear asignaturas
        $this->info("\nCreando asignaturas para " . count($disciplinas) . " disciplinas...");
        
        $asignaturas = [];
        foreach ($disciplinas as $disciplina) {
            for ($i = 1; $i <= 3; $i++) {
                $id = DB::table('asignaturas')->insertGetId([
                    'nombre' => $faker->word . ' ' . $i,
                    'disciplina_id' => $disciplina,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $asignaturas[] = $id;
            }
        }
        $this->line("   ✓ Creadas " . count($asignaturas) . " asignaturas");

        // 3. Crear curículos
        $this->info("\nCreando curículos...");
        $curriculos = 0;
        foreach ($asignaturas as $asignatura) {
            DB::table('curriculos')->insert([
                'nombre' => 'Currículo ' . $faker->year,
                'asignatura_id' => $asignatura,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $curriculos++;
        }
        $this->line("   ✓ Creados " . $curriculos . " curículos");

        // 4. Asignar asignaturas a profesores
        $this->info("\nAsignando asignaturas a profesores...");
        
        $profesores = DB::table('profesores')->get();
        $asignaturasCount = count($asignaturas);
        $relationesCreadas = 0;

        foreach ($profesores as $profesor) {
            // Cada profesor enseña 2-4 asignaturas
            $asignaturasPerProfesor = rand(2, 4);
            $asignaturasAleatorias = array_rand(array_flip($asignaturas), min($asignaturasPerProfesor, $asignaturasCount));
            
            if (!is_array($asignaturasAleatorias)) {
                $asignaturasAleatorias = [$asignaturasAleatorias];
            }

            foreach ($asignaturasAleatorias as $asignatura) {
                // Verificar que no exista ya
                $existe = DB::table('profesor_asignatura')
                    ->where('profesor_id', $profesor->id)
                    ->where('asignatura_id', $asignatura)
                    ->first();

                if (!$existe) {
                    DB::table('profesor_asignatura')->insert([
                        'profesor_id' => $profesor->id,
                        'asignatura_id' => $asignatura,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $relationesCreadas++;
                }
            }
        }
        $this->line("   ✓ Creadas " . $relationesCreadas . " relaciones profesor-asignatura");

        // 5. Verificar estado final
        $this->info("\n=== ESTADO FINAL ===");
        $this->line("Disciplinas: " . DB::table('disciplinas')->count());
        $this->line("Asignaturas: " . DB::table('asignaturas')->count());
        $this->line("Curículos: " . DB::table('curriculos')->count());
        $this->line("Profesor-Asignatura: " . DB::table('profesor_asignatura')->count());
        
        $this->info("\n✓ Datos completados exitosamente!");
    }
}
