<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class FakeDataSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('es_ES');

        // FACULTADES
        $fmfcId = DB::table('facultad')->insertGetId([
            'nombre' => 'FMFC',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $humanidadesId = DB::table('facultad')->insertGetId([
            'nombre' => 'Facultad de Humanidades',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // DEPARTAMENTOS para FMFC
        $deptInfoId = DB::table('departamento')->insertGetId([
            'nombre' => 'Departamento de Informática',
            'facultad_id' => $fmfcId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $deptMatId = DB::table('departamento')->insertGetId([
            'nombre' => 'Departamento de Matemáticas',
            'facultad_id' => $fmfcId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $deptFisId = DB::table('departamento')->insertGetId([
            'nombre' => 'Departamento de Física',
            'facultad_id' => $fmfcId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // DEPARTAMENTO para Humanidades
        $deptHumId = DB::table('departamento')->insertGetId([
            'nombre' => 'Departamento de Lengua y Literatura',
            'facultad_id' => $humanidadesId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // CARRERAS con años definidos
        $carrerasConfig = [
            // FMFC carreras (4 años)
            ['nombre' => 'Ingeniería Informática', 'facultad' => $fmfcId, 'anos' => 4],
            ['nombre' => 'Ciencias de la Información', 'facultad' => $fmfcId, 'anos' => 4],
            ['nombre' => 'Ciencias de la Computación', 'facultad' => $fmfcId, 'anos' => 4],
            ['nombre' => 'Física', 'facultad' => $fmfcId, 'anos' => 5],
            ['nombre' => 'Matemática', 'facultad' => $fmfcId, 'anos' => 5],
            // Humanidades carreras
            ['nombre' => 'Letras', 'facultad' => $humanidadesId, 'anos' => 4],
            ['nombre' => 'Lengua Inglesa', 'facultad' => $humanidadesId, 'anos' => 5],
        ];

        $carreras = [];
        foreach ($carrerasConfig as $config) {
            $carreraId = DB::table('carrera')->insertGetId([
                'nombre' => $config['nombre'],
                'facultad_id' => $config['facultad'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $carreras[$carreraId] = $config['anos'];
        }

        // PLANES DE ESTUDIO (4 por carrera: a, b, c, d)
        $planes = [];
        $planesNombres = ['a', 'b', 'c', 'd'];
        $informaticaCarreraId = null;
        
        foreach ($carreras as $carreraId => $anos) {
            // Identificar la carrera Informática
            if ($informaticaCarreraId === null) {
                $carreraInfo = DB::table('carrera')->find($carreraId);
                if ($carreraInfo && strpos(strtolower($carreraInfo->nombre), 'informatica') !== false) {
                    $informaticaCarreraId = $carreraId;
                }
            }
            
            for ($j = 0; $j < 4; $j++) {
                $planes[] = DB::table('plan_estudio')->insertGetId([
                    'nombre' => $planesNombres[$j],
                    'carrera_id' => $carreraId,
                    'fecha_inicio' => (2020 + $j) . '-09-01',
                    'fecha_fin' => null,
                    'vigente' => $j == 3,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // DISCIPLINAS
        $disciplinas = [];
        foreach ($planes as $plan) {
            for ($i = 1; $i <= 4; $i++) {
                $disciplinas[] = DB::table('disciplina')->insertGetId([
                    'nombre' => "Disciplina " . chr(64 + $i),
                    'plan_estudio_id' => $plan,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // ASIGNATURAS
        $asignaturas = [];
        foreach ($disciplinas as $disciplina) {
            for ($i = 1; $i <= 3; $i++) {
                $asignaturas[] = DB::table('asignatura')->insertGetId([
                    'nombre' => $faker->word . ' ' . $i,
                    'disciplina_id' => $disciplina,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // CURRICULOS
        foreach ($asignaturas as $asignatura) {
            DB::table('curriculo')->insert([
                'nombre' => 'Currículo ' . $faker->year,
                'asignatura_id' => $asignatura,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // AÑOS ACADÉMICOS (basado en la cantidad de años de cada carrera)
        $anios = [];
        $carreraIndex = 0;
        
        foreach ($carreras as $carreraId => $anosCarrera) {
            for ($a = 1; $a <= $anosCarrera; $a++) {
                $anios[] = DB::table('anio_academico')->insertGetId([
                    'nombre' => "{$a}º año - Carrera {$carreraIndex} - 2025-2026",
                    'carrera_id' => $carreraId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            $carreraIndex++;
        }

        // GRUPOS
        $grupos = [];
        foreach ($anios as $anio) {
            for ($i = 1; $i <= 3; $i++) {
                $grupos[] = DB::table('grupo')->insertGetId([
                    'nombre' => "Grupo " . chr(64 + $i),
                    'anio_academico_id' => $anio,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // ALUMNOS
        $alumnos = [];
        foreach ($grupos as $grupo) {
            $alumnosPerGrupo = $faker->numberBetween(30, 40);
            for ($i = 0; $i < $alumnosPerGrupo; $i++) {
                $alumnos[] = DB::table('estudiante')->insertGetId([
                    'nombre' => $faker->name,
                    'email' => $faker->unique()->safeEmail,
                    'grupo_id' => $grupo,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // CATEGORÍAS INDICADOR
        $categorias = [];
        foreach (['Docencia', 'Investigación', 'Extensión', 'Gestión Académica'] as $cat) {
            $categorias[] = DB::table('categoria_indicador')->insertGetId([
                'nombre' => $cat,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // INDICADORES
        $indicadores = [];
        foreach ($categorias as $categoria) {
            for ($i = 0; $i < 3; $i++) {
                $indicadores[] = DB::table('indicador')->insertGetId([
                    'nombre' => 'Indicador ' . $faker->word,
                    'categoria_indicador_id' => $categoria,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // EVALUACIONES INTEGRALES
        $calificaciones = ['E', 'B', 'MB'];
        foreach ($alumnos as $alumno) {
            DB::table('evaluacion_integral')->insert([
                'alumno_id' => $alumno,
                'anio_academico_id' => $faker->randomElement($anios),
                'evaluacion_docencia' => $faker->randomElement($calificaciones),
                'investigacion' => $faker->randomElement($calificaciones),
                'comportamiento_social' => $faker->randomElement($calificaciones),
                'actividades_extension' => $faker->randomElement($calificaciones),
                'formacion_politica' => $faker->randomElement($calificaciones),
                'tareas_impacto' => $faker->randomElement($calificaciones),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // BAJAS
        $causas = [
            'insuficiencia_docente',
            'sancion_disciplinaria',
            'voluntaria',
            'inasistencia',
            'desercion',
            'definitiva',
            'otras_causas'
        ];

        $observacionesEjemplos = [
            'El estudiante no presentó suficiente dedicación',
            'Bajo rendimiento académico sostenido',
            'Inasistencia a clases sin justificación',
            'Violación de normas de conducta',
            'Retiro voluntario del programa',
            'Problemas personales',
            'Cambio de carrera',
            'Problemas económicos',
            'Incompatibilidad con el programa',
            'Recomendación del tutor académico'
        ];

        $totalAlumnos = count($alumnos);
        $bajasACrear = intval($totalAlumnos * $faker->numberBetween(10, 20) / 100);

        for ($i = 0; $i < $bajasACrear; $i++) {
            $alumnoAleatorio = $faker->randomElement($alumnos);
            
            $existente = DB::table('baja_estudiante')
                ->where('alumno_id', $alumnoAleatorio)
                ->first();
            
            if (!$existente) {
                DB::table('baja_estudiante')->insert([
                    'alumno_id' => $alumnoAleatorio,
                    'causa' => $faker->randomElement($causas),
                    'observaciones' => $faker->randomElement($observacionesEjemplos),
                    'created_at' => $faker->dateTimeBetween('-1 year', 'now'),
                    'updated_at' => now(),
                ]);
            }
        }

        // ============= NUEVAS TABLAS =============

        // CATEGORÍAS DOCENTES
        $categoriasDocentes = [];
        foreach (['Asistente', 'Instructor', 'Profesor Auxiliar', 'Profesor', 'Profesor Titular'] as $cat) {
            $categoriasDocentes[] = DB::table('categoria_docente')->insertGetId([
                'nombre' => $cat,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // CATEGORÍAS CIENTÍFICAS
        $categoriasCientificas = [];
        foreach (['Novicio', 'Juntor', 'Senior', 'Investigador Principal'] as $cat) {
            $categoriasCientificas[] = DB::table('categoria_cientifica')->insertGetId([
                'nombre' => $cat,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // GRADOS DE TÍTULO
        $gradosTitulo = [];
        foreach (['Licenciado', 'Master', 'Doctor', 'Postdoctor'] as $grado) {
            $gradosTitulo[] = DB::table('grado_titulo')->insertGetId([
                'nombre' => $grado,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // PROFESORES
        $profesores = [];
        $departamentosArray = [$deptInfoId, $deptMatId, $deptFisId, $deptHumId];
        
        for ($i = 0; $i < 30; $i++) {
            $profesores[] = DB::table('profesor')->insertGetId([
                'nombre' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'cedula' => $faker->unique()->numerify('##########'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // PROFESOR - CATEGORÍA - GRADO (cada profesor tiene entre 1-3 combinaciones)
        foreach ($profesores as $profesor) {
            $combinaciones = $faker->numberBetween(1, 3);
            for ($i = 0; $i < $combinaciones; $i++) {
                DB::table('profesor_categoria_grado')->insert([
                    'profesor_id' => $profesor,
                    'categoria_docente_id' => $faker->randomElement($categoriasDocentes),
                    'categoria_cientifica_id' => $faker->randomElement($categoriasCientificas),
                    'grado_titulo_id' => $faker->randomElement($gradosTitulo),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // MIEMBRO DE DEPARTAMENTO (cada profesor puede pertenecer a 1-2 departamentos)
        foreach ($profesores as $profesor) {
            $deptCount = $faker->numberBetween(1, 2);
            $selectedDepts = array_slice($departamentosArray, 0, $deptCount);
            
            foreach ($selectedDepts as $deptId) {
                $exists = DB::table('miembro_departamento')
                    ->where('profesor_id', $profesor)
                    ->where('departamento_id', $deptId)
                    ->exists();
                
                if (!$exists) {
                    DB::table('miembro_departamento')->insert([
                        'profesor_id' => $profesor,
                        'departamento_id' => $deptId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // JEFE DE DEPARTAMENTO (un profesor es jefe de un departamento)
        $departamentosConJefe = [];
        foreach ($departamentosArray as $deptId) {
            if (!in_array($deptId, $departamentosConJefe)) {
                $jefe = $faker->randomElement($profesores);
                DB::table('jefe_departamento')->insert([
                    'profesor_id' => $jefe,
                    'departamento_id' => $deptId,
                    'fecha_inicio' => now()->subYear(),
                    'fecha_fin' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $departamentosConJefe[] = $deptId;
            }
        }

        // ALUMNO AYUDANTE (algunos alumnos son ayudantes en asignaturas)
        $alumnosAyudantes = [];
        foreach (array_slice($alumnos, 0, intval(count($alumnos) * 0.15)) as $alumno) {
            $asignatura = $faker->randomElement($asignaturas);
            
            $exists = DB::table('estudiante_ayudante')
                ->where('alumno_id', $alumno)
                ->where('asignatura_id', $asignatura)
                ->exists();
            
            if (!$exists) {
                $alumnosAyudantes[] = DB::table('estudiante_ayudante')->insertGetId([
                    'alumno_id' => $alumno,
                    'asignatura_id' => $asignatura,
                    'fecha_inicio' => now()->subMonths($faker->numberBetween(1, 6)),
                    'fecha_fin' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // FACULTAD - DEPARTAMENTO
        foreach ($departamentosArray as $deptId) {
            $dept = DB::table('departamento')->find($deptId);
            if ($dept) {
                $exists = DB::table('facultad_departamento')
                    ->where('facultad_id', $dept->facultad_id)
                    ->where('departamento_id', $deptId)
                    ->exists();
                
                if (!$exists) {
                    DB::table('facultad_departamento')->insert([
                        'facultad_id' => $dept->facultad_id,
                        'departamento_id' => $deptId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // CARRERA - DEPARTAMENTO (asociar algunas carreras con departamentos)
        $carrerasArray = array_keys($carreras);
        foreach (array_slice($carrerasArray, 0, 5) as $carreraId) {
            $deptId = $faker->randomElement($departamentosArray);
            
            $exists = DB::table('carrera_departamento')
                ->where('carrera_id', $carreraId)
                ->where('departamento_id', $deptId)
                ->exists();
            
            if (!$exists) {
                DB::table('carrera_departamento')->insert([
                    'carrera_id' => $carreraId,
                    'departamento_id' => $deptId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // CARRERA - PLAN
        foreach (array_keys($carreras) as $carreraId) {
            $planesCarrera = DB::table('plan_estudio')
                ->where('carrera_id', $carreraId)
                ->pluck('id')
                ->toArray();
            
            foreach ($planesCarrera as $planId) {
                $exists = DB::table('carrera_plan')
                    ->where('carrera_id', $carreraId)
                    ->where('plan_estudio_id', $planId)
                    ->exists();
                
                if (!$exists) {
                    DB::table('carrera_plan')->insert([
                        'carrera_id' => $carreraId,
                        'plan_estudio_id' => $planId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // CURSO ACADÉMICO
        $cursosAcademicos = [];
        for ($year = 2023; $year <= 2026; $year++) {
            $cursosAcademicos[] = DB::table('curso_academico')->insertGetId([
                'nombre' => "Curso $year-" . ($year + 1),
                'fecha_inicio' => "$year-09-01 00:00:00",
                'fecha_fin' => ($year + 1) . "-08-31 23:59:59",
                'activo' => $year == 2025,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // VERSIONES
        $versiones = [];
        foreach (['v1.0', 'v1.1', 'v2.0', 'v2.1'] as $version) {
            $versiones[] = DB::table('version')->insertGetId([
                'nombre' => $version,
                'descripcion' => "Versión $version del sistema académico",
                'creada_por' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // MODIFICACIONES
        $modificaciones = [];
        $tablas = ['estudiante', 'profesor', 'carrera', 'departamento', 'grupo', 'indicador'];
        
        for ($i = 0; $i < 50; $i++) {
            $modificaciones[] = DB::table('modificacion')->insertGetId([
                'tabla' => $faker->randomElement($tablas),
                'registro_id' => $faker->numberBetween(1, 100),
                'tipo' => $faker->randomElement(['create', 'update', 'delete']),
                'datos_anteriores' => json_encode(['campo' => 'valor_anterior']),
                'datos_nuevos' => json_encode(['campo' => 'valor_nuevo']),
                'usuario_id' => 1,
                'created_at' => $faker->dateTimeBetween('-1 year', 'now'),
                'updated_at' => now(),
            ]);
        }

        // VERSION - MODIFICACIONES
        foreach ($versiones as $versionId) {
            $modCount = $faker->numberBetween(5, 15);
            $selectedMods = array_slice($modificaciones, 0, $modCount);
            
            foreach ($selectedMods as $modId) {
                $exists = DB::table('version_modificacion')
                    ->where('version_id', $versionId)
                    ->where('modificacion_id', $modId)
                    ->exists();
                
                if (!$exists) {
                    DB::table('version_modificacion')->insert([
                        'version_id' => $versionId,
                        'modificacion_id' => $modId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // INDICADORES POR FACULTAD
        foreach ($categorias as $indicador) {
            $cursoActivo = end($cursosAcademicos);
            
            $exists = DB::table('indicador_facultad')
                ->where('indicador_id', $indicador)
                ->where('facultad_id', $fmfcId)
                ->where('curso_academico_id', $cursoActivo)
                ->exists();
            
            if (!$exists) {
                DB::table('indicador_facultad')->insert([
                    'indicador_id' => $indicador,
                    'facultad_id' => $fmfcId,
                    'curso_academico_id' => $cursoActivo,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // INDICADORES POR DEPARTAMENTO
        foreach (array_slice($indicadores, 0, 8) as $indicador) {
            $deptId = $faker->randomElement($departamentosArray);
            $cursoActivo = end($cursosAcademicos);
            
            $exists = DB::table('indicador_departamento')
                ->where('indicador_id', $indicador)
                ->where('departamento_id', $deptId)
                ->where('curso_academico_id', $cursoActivo)
                ->exists();
            
            if (!$exists) {
                DB::table('indicador_departamento')->insert([
                    'indicador_id' => $indicador,
                    'departamento_id' => $deptId,
                    'curso_academico_id' => $cursoActivo,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // INDICADORES POR CARRERA
        foreach (array_slice($indicadores, 0, 10) as $indicador) {
            $carreraId = $faker->randomElement(array_keys($carreras));
            $cursoActivo = end($cursosAcademicos);
            
            $exists = DB::table('indicador_carrera')
                ->where('indicador_id', $indicador)
                ->where('carrera_id', $carreraId)
                ->where('curso_academico_id', $cursoActivo)
                ->exists();
            
            if (!$exists) {
                DB::table('indicador_carrera')->insert([
                    'indicador_id' => $indicador,
                    'carrera_id' => $carreraId,
                    'curso_academico_id' => $cursoActivo,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // INDICADORES POR GRUPO
        foreach (array_slice($indicadores, 0, 5) as $indicador) {
            $grupoId = $faker->randomElement($grupos);
            $cursoActivo = end($cursosAcademicos);
            
            $exists = DB::table('indicador_grupo')
                ->where('indicador_id', $indicador)
                ->where('grupo_id', $grupoId)
                ->where('curso_academico_id', $cursoActivo)
                ->exists();
            
            if (!$exists) {
                DB::table('indicador_grupo')->insert([
                    'indicador_id' => $indicador,
                    'grupo_id' => $grupoId,
                    'curso_academico_id' => $cursoActivo,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // INDICADORES POR AÑO ACADÉMICO
        foreach (array_slice($indicadores, 0, 6) as $indicador) {
            $anioId = $faker->randomElement($anios);
            $cursoActivo = end($cursosAcademicos);
            
            $exists = DB::table('indicador_anno')
                ->where('indicador_id', $indicador)
                ->where('anio_academico_id', $anioId)
                ->where('curso_academico_id', $cursoActivo)
                ->exists();
            
            if (!$exists) {
                DB::table('indicador_anno')->insert([
                    'indicador_id' => $indicador,
                    'anio_academico_id' => $anioId,
                    'curso_academico_id' => $cursoActivo,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // INDICADORES POR CATEGORÍA
        foreach (array_slice($indicadores, 0, 6) as $indicador) {
            $categoriaId = $faker->randomElement($categorias);
            
            $exists = DB::table('indicador_categoria')
                ->where('indicador_id', $indicador)
                ->where('categoria_indicador_id', $categoriaId)
                ->exists();
            
            if (!$exists) {
                DB::table('indicador_categoria')->insert([
                    'indicador_id' => $indicador,
                    'categoria_indicador_id' => $categoriaId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // INDICADORES POR CURSO ACADÉMICO
        foreach (array_slice($indicadores, 0, 8) as $indicador) {
            $cursoId = $faker->randomElement($cursosAcademicos);
            
            $exists = DB::table('indicador_curso')
                ->where('indicador_id', $indicador)
                ->where('curso_academico_id', $cursoId)
                ->exists();
            
            if (!$exists) {
                DB::table('indicador_curso')->insert([
                    'indicador_id' => $indicador,
                    'curso_academico_id' => $cursoId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
