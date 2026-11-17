<?php

namespace Database\Seeders;

use App\Models\AnioAcademico;
use App\Models\Asignatura;
use App\Models\Carrera;
use App\Models\Curriculo;
use App\Models\Departamento;
use App\Models\Disciplina;
use App\Models\Facultad;
use App\Models\Grupo;
use App\Models\PlanEstudio;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AcademicCatalogSeeder extends Seeder
{
    private const PLAN_NAME = 'Plan de Estudio E';

    public function run(): void
    {
        DB::transaction(function (): void {
            $this->seedReferenceData();

            foreach ($this->faculties() as $facultyData) {
                $faculty = Facultad::query()->updateOrCreate(
                    ['nombre' => $facultyData['nombre']],
                    ['nombre' => $facultyData['nombre']],
                );

                foreach ($facultyData['departamentos'] as $departmentData) {
                    $department = Departamento::query()->updateOrCreate(
                        ['facultad_id' => $faculty->id, 'nombre' => $departmentData['nombre']],
                        ['nombre' => $departmentData['nombre']],
                    );

                    DB::table('facultad_departamento')->updateOrInsert([
                        'facultad_id' => $faculty->id,
                        'departamento_id' => $department->id,
                    ], $this->timestamps());

                    foreach ($departmentData['carreras'] as $careerData) {
                        $this->seedCareer($faculty, $department, $careerData);
                    }
                }
            }
        });
    }

    /**
     * @param  array{nombre: string, codigo: string, disciplinas: list<array{nombre: string, asignaturas: list<string>}>}  $careerData
     */
    private function seedCareer(Facultad $faculty, Departamento $department, array $careerData): void
    {
        $career = Carrera::query()->updateOrCreate(
            ['facultad_id' => $faculty->id, 'nombre' => $careerData['nombre']],
            ['nombre' => $careerData['nombre']],
        );

        DB::table('carrera_departamento')->updateOrInsert([
            'carrera_id' => $career->id,
            'departamento_id' => $department->id,
        ], $this->timestamps());

        $plan = PlanEstudio::query()->updateOrCreate(
            ['carrera_id' => $career->id, 'nombre' => self::PLAN_NAME],
            ['fecha_inicio' => '2025-09-01', 'fecha_fin' => null, 'vigente' => true],
        );

        DB::table('carrera_plan')->updateOrInsert([
            'carrera_id' => $career->id,
            'plan_estudio_id' => $plan->id,
        ], $this->timestamps());

        $academicYears = $this->seedAcademicYears($career, $careerData['codigo']);
        $subjectIndex = 0;

        foreach ($careerData['disciplinas'] as $disciplineData) {
            $discipline = Disciplina::query()->updateOrCreate(
                ['plan_estudio_id' => $plan->id, 'nombre' => $disciplineData['nombre']],
                ['nombre' => $disciplineData['nombre']],
            );

            foreach ($disciplineData['asignaturas'] as $subjectName) {
                $level = intdiv($subjectIndex, 3) + 1;
                $subjectCode = sprintf('%s-%03d', $careerData['codigo'], 101 + $subjectIndex);
                $subject = Asignatura::query()->updateOrCreate(
                    ['codigo' => $subjectCode],
                    [
                        'nombre' => $subjectName,
                        'disciplina_id' => $discipline->id,
                        'horas' => 48 + (($subjectIndex % 3) * 16),
                    ],
                );

                $curriculum = Curriculo::query()->updateOrCreate(
                    ['asignatura_id' => $subject->id],
                    ['nombre' => "Programa de {$subjectName}"],
                );

                DB::table('curriculo_plan')->updateOrInsert([
                    'curriculo_id' => $curriculum->id,
                    'plan_estudio_id' => $plan->id,
                ], $this->timestamps());

                DB::table('anio_asignatura')->updateOrInsert([
                    'anio_academico_id' => $academicYears[$level]->id,
                    'asignatura_id' => $subject->id,
                ], $this->timestamps());

                $subjectIndex++;
            }
        }
    }

    /**
     * @return array<int, AnioAcademico>
     */
    private function seedAcademicYears(Carrera $career, string $careerCode): array
    {
        $academicYears = [];

        foreach (range(1, 4) as $level) {
            $academicYear = AnioAcademico::query()->updateOrCreate(
                ['carrera_id' => $career->id, 'nivel' => $level],
                ['nombre' => "{$level}.º año · {$career->nombre}"],
            );

            foreach (['A', 'B'] as $section) {
                Grupo::query()->updateOrCreate(
                    ['anio_academico_id' => $academicYear->id, 'nombre' => "{$careerCode}-{$level}{$section}"],
                    ['nombre' => "{$careerCode}-{$level}{$section}"],
                );
            }

            $academicYears[$level] = $academicYear;
        }

        return $academicYears;
    }

    private function seedReferenceData(): void
    {
        foreach (['Instructor', 'Profesor Asistente', 'Profesor Auxiliar', 'Profesor Titular'] as $name) {
            DB::table('categoria_docente')->updateOrInsert(['nombre' => $name], $this->timestamps());
        }

        foreach (['Aspirante a Investigador', 'Investigador Agregado', 'Investigador Auxiliar', 'Investigador Titular'] as $name) {
            DB::table('categoria_cientifica')->updateOrInsert(['nombre' => $name], $this->timestamps());
        }

        foreach (['Licenciado', 'Máster en Ciencias', 'Doctor en Ciencias'] as $name) {
            DB::table('grado_titulo')->updateOrInsert(['nombre' => $name], $this->timestamps());
        }

        foreach (['Numérico', 'Porcentual', 'Cualitativo', 'Booleano'] as $name) {
            DB::table('categoria_indicador')->updateOrInsert(['nombre' => $name], $this->timestamps());
        }
    }

    /**
     * @return array{created_at: Carbon, updated_at: Carbon}
     */
    private function timestamps(): array
    {
        return ['created_at' => now(), 'updated_at' => now()];
    }

    /**
     * @return list<array{nombre: string, departamentos: list<array{nombre: string, carreras: list<array{nombre: string, codigo: string, disciplinas: list<array{nombre: string, asignaturas: list<string>}>}>}>}>
     */
    private function faculties(): array
    {
        return [
            [
                'nombre' => 'Facultad de Matemática, Física y Computación',
                'departamentos' => [
                    [
                        'nombre' => 'Departamento de Informática',
                        'carreras' => [
                            [
                                'nombre' => 'Ingeniería Informática',
                                'codigo' => 'INF',
                                'disciplinas' => [
                                    ['nombre' => 'Programación y Algoritmos', 'asignaturas' => ['Introducción a la Programación', 'Programación Orientada a Objetos', 'Estructuras de Datos y Algoritmos']],
                                    ['nombre' => 'Sistemas Computacionales', 'asignaturas' => ['Arquitectura de Computadoras', 'Sistemas Operativos', 'Redes de Computadoras']],
                                    ['nombre' => 'Ingeniería de Software', 'asignaturas' => ['Bases de Datos', 'Ingeniería de Software', 'Desarrollo de Aplicaciones Web']],
                                    ['nombre' => 'Computación Avanzada', 'asignaturas' => ['Matemática Discreta', 'Inteligencia Artificial', 'Seguridad Informática']],
                                ],
                            ],
                            [
                                'nombre' => 'Ciencias de la Computación',
                                'codigo' => 'CCO',
                                'disciplinas' => [
                                    ['nombre' => 'Fundamentos de Programación', 'asignaturas' => ['Programación I', 'Programación II', 'Estructuras de Datos']],
                                    ['nombre' => 'Matemática Computacional', 'asignaturas' => ['Álgebra Lineal', 'Matemática Discreta', 'Análisis de Algoritmos']],
                                    ['nombre' => 'Sistemas de Computación', 'asignaturas' => ['Sistemas Operativos', 'Bases de Datos', 'Computación Paralela']],
                                    ['nombre' => 'Computación Teórica e Inteligente', 'asignaturas' => ['Teoría de la Computación', 'Aprendizaje Automático', 'Criptografía']],
                                ],
                            ],
                        ],
                    ],
                    [
                        'nombre' => 'Departamento de Matemática',
                        'carreras' => [[
                            'nombre' => 'Matemática',
                            'codigo' => 'MAT',
                            'disciplinas' => [
                                ['nombre' => 'Álgebra y Geometría', 'asignaturas' => ['Álgebra I', 'Álgebra Lineal', 'Geometría Analítica']],
                                ['nombre' => 'Análisis Matemático', 'asignaturas' => ['Cálculo I', 'Cálculo II', 'Análisis Real']],
                                ['nombre' => 'Matemática Aplicada', 'asignaturas' => ['Ecuaciones Diferenciales', 'Análisis Numérico', 'Investigación de Operaciones']],
                                ['nombre' => 'Probabilidad y Estructuras', 'asignaturas' => ['Probabilidad y Estadística', 'Topología', 'Variable Compleja']],
                            ],
                        ]],
                    ],
                    [
                        'nombre' => 'Departamento de Física',
                        'carreras' => [[
                            'nombre' => 'Física',
                            'codigo' => 'FIS',
                            'disciplinas' => [
                                ['nombre' => 'Física General', 'asignaturas' => ['Mecánica Clásica', 'Electromagnetismo', 'Termodinámica']],
                                ['nombre' => 'Métodos Experimentales', 'asignaturas' => ['Laboratorio de Física I', 'Óptica', 'Instrumentación Física']],
                                ['nombre' => 'Física Moderna', 'asignaturas' => ['Mecánica Cuántica', 'Física Estadística', 'Física del Estado Sólido']],
                                ['nombre' => 'Métodos Matemáticos', 'asignaturas' => ['Cálculo para Física', 'Métodos Matemáticos para la Física', 'Física Nuclear']],
                            ],
                        ]],
                    ],
                ],
            ],
            [
                'nombre' => 'Facultad de Comunicación y Humanidades',
                'departamentos' => [
                    [
                        'nombre' => 'Departamento de Ciencias de la Información',
                        'carreras' => [[
                            'nombre' => 'Ciencias de la Información',
                            'codigo' => 'CIN',
                            'disciplinas' => [
                                ['nombre' => 'Fundamentos de Información', 'asignaturas' => ['Fundamentos de Ciencias de la Información', 'Fuentes y Servicios de Información', 'Estudios de Usuarios']],
                                ['nombre' => 'Organización Documental', 'asignaturas' => ['Organización de la Información', 'Descripción Bibliográfica', 'Gestión Documental']],
                                ['nombre' => 'Tecnologías de Información', 'asignaturas' => ['Sistemas de Información', 'Recuperación de Información', 'Bibliotecas Digitales']],
                                ['nombre' => 'Gestión y Preservación', 'asignaturas' => ['Gestión del Conocimiento', 'Preservación Digital', 'Alfabetización Informacional']],
                            ],
                        ]],
                    ],
                    [
                        'nombre' => 'Departamento de Letras',
                        'carreras' => [[
                            'nombre' => 'Letras',
                            'codigo' => 'LET',
                            'disciplinas' => [
                                ['nombre' => 'Lengua y Lingüística', 'asignaturas' => ['Gramática Española', 'Lingüística General', 'Semiótica']],
                                ['nombre' => 'Literaturas Nacionales', 'asignaturas' => ['Literatura Cubana I', 'Literatura Latinoamericana', 'Literatura Española']],
                                ['nombre' => 'Teoría y Crítica', 'asignaturas' => ['Introducción a los Estudios Literarios', 'Teoría Literaria', 'Crítica Literaria']],
                                ['nombre' => 'Investigación y Edición', 'asignaturas' => ['Literatura Universal', 'Edición de Textos', 'Taller de Investigación Literaria']],
                            ],
                        ]],
                    ],
                    [
                        'nombre' => 'Departamento de Lengua Inglesa',
                        'carreras' => [[
                            'nombre' => 'Lengua Inglesa',
                            'codigo' => 'ING',
                            'disciplinas' => [
                                ['nombre' => 'Práctica Integral de la Lengua', 'asignaturas' => ['Lengua Inglesa I', 'Lengua Inglesa II', 'Comprensión y Expresión Oral']],
                                ['nombre' => 'Estudios Lingüísticos', 'asignaturas' => ['Fonética y Fonología Inglesas', 'Gramática Inglesa I', 'Lingüística Inglesa']],
                                ['nombre' => 'Traducción e Interpretación', 'asignaturas' => ['Traducción General', 'Interpretación', 'Traducción Especializada']],
                                ['nombre' => 'Cultura y Didáctica', 'asignaturas' => ['Literatura Inglesa', 'Cultura de los Países Anglófonos', 'Metodología de la Enseñanza del Inglés']],
                            ],
                        ]],
                    ],
                ],
            ],
        ];
    }
}
