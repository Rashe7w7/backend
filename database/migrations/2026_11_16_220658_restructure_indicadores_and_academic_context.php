<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('indicador')->delete();
        DB::table('categoria_indicador')->delete();

        $now = now();
        DB::table('categoria_indicador')->insert([
            ['nombre' => 'Numérico', 'created_at' => $now, 'updated_at' => $now],
            ['nombre' => 'Porcentual', 'created_at' => $now, 'updated_at' => $now],
            ['nombre' => 'Cualitativo', 'created_at' => $now, 'updated_at' => $now],
            ['nombre' => 'Booleano', 'created_at' => $now, 'updated_at' => $now],
        ]);

        Schema::table('indicador', function (Blueprint $table) {
            $table->string('especificacion')->after('categoria_indicador_id')->index();
        });

        if (! Schema::hasColumn('anio_academico', 'nivel')) {
            Schema::table('anio_academico', function (Blueprint $table) {
                $table->unsignedTinyInteger('nivel')->nullable()->after('nombre')->index();
            });
        }

        DB::table('anio_academico')
            ->select(['id', 'nombre'])
            ->orderBy('id')
            ->each(function (object $anio): void {
                preg_match('/([1-5])/', $anio->nombre, $matches);

                if (isset($matches[1])) {
                    DB::table('anio_academico')
                        ->where('id', $anio->id)
                        ->update(['nivel' => (int) $matches[1]]);
                }
            });

        if (DB::table('anio_academico')->whereNull('nivel')->exists()) {
            throw new RuntimeException('No se pudo determinar el nivel de todos los años académicos.');
        }

        Schema::table('anio_academico', function (Blueprint $table) {
            $table->unsignedTinyInteger('nivel')->nullable(false)->change();
        });

        $this->consolidateAcademicCourses();

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE indicador ADD CONSTRAINT indicador_especificacion_check CHECK (especificacion IN ('carrera', 'facultad', 'departamento', 'grupo', 'nivel_academico'))");
            DB::statement('ALTER TABLE anio_academico ADD CONSTRAINT anio_academico_nivel_check CHECK (nivel BETWEEN 1 AND 5)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE indicador DROP CONSTRAINT IF EXISTS indicador_especificacion_check');
            DB::statement('ALTER TABLE anio_academico DROP CONSTRAINT IF EXISTS anio_academico_nivel_check');
        }

        Schema::table('indicador', function (Blueprint $table) {
            $table->dropColumn('especificacion');
        });

        Schema::table('anio_academico', function (Blueprint $table) {
            $table->dropColumn('nivel');
        });
    }

    private function consolidateAcademicCourses(): void
    {
        $canonicalByName = [];
        $referenceTables = [
            'indicador_curso',
            'indicador_facultad',
            'indicador_departamento',
            'indicador_carrera',
            'indicador_grupo',
            'indicador_anno',
            'profesor_asignatura',
            'estudiante_ayudante',
        ];

        DB::table('curso_academico')
            ->orderBy('id')
            ->get()
            ->each(function (object $curso) use (&$canonicalByName, $referenceTables): void {
                $normalizedName = preg_replace('/^Curso\s+/i', '', trim($curso->nombre));

                if (! isset($canonicalByName[$normalizedName])) {
                    $canonicalByName[$normalizedName] = $curso->id;
                    DB::table('curso_academico')
                        ->where('id', $curso->id)
                        ->update(['nombre' => $normalizedName]);

                    return;
                }

                $canonicalId = $canonicalByName[$normalizedName];

                foreach ($referenceTables as $table) {
                    if (Schema::hasTable($table) && Schema::hasColumn($table, 'curso_academico_id')) {
                        DB::table($table)
                            ->where('curso_academico_id', $curso->id)
                            ->update(['curso_academico_id' => $canonicalId]);
                    }
                }

                DB::table('curso_academico')->where('id', $curso->id)->delete();
            });
    }
};
