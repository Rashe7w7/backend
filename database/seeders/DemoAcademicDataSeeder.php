<?php

namespace Database\Seeders;

use App\Models\Alumno;
use App\Models\AlumnoAyudante;
use App\Models\CursoAcademico;
use App\Models\Profesor;
use App\Models\ProfesorAsignatura;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DemoAcademicDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $professorsByDepartment = $this->seedProfessors();
            $assignmentsByCareer = $this->seedTeachingAssignments($professorsByDepartment);
            $this->seedStudents($assignmentsByCareer);
        });
    }

    /**
     * @return array<int, list<int>>
     */
    private function seedProfessors(): array
    {
        $professors = [
            ['nombre' => 'Dra. Laura Fernández Castro', 'email' => 'laura.fernandez@universidad.test', 'cedula' => 'DOC-0001', 'departamento' => 'Departamento de Informática', 'docente' => 'Profesor Titular', 'cientifica' => 'Investigador Titular', 'grado' => 'Doctor en Ciencias'],
            ['nombre' => 'MSc. Ernesto Valdés Pérez', 'email' => 'ernesto.valdes@universidad.test', 'cedula' => 'DOC-0002', 'departamento' => 'Departamento de Informática', 'docente' => 'Profesor Auxiliar', 'cientifica' => 'Investigador Auxiliar', 'grado' => 'Máster en Ciencias'],
            ['nombre' => 'Dra. Claudia Hernández León', 'email' => 'claudia.hernandez@universidad.test', 'cedula' => 'DOC-0003', 'departamento' => 'Departamento de Matemática', 'docente' => 'Profesor Titular', 'cientifica' => 'Investigador Titular', 'grado' => 'Doctor en Ciencias'],
            ['nombre' => 'Dr. Manuel Ortega Díaz', 'email' => 'manuel.ortega@universidad.test', 'cedula' => 'DOC-0004', 'departamento' => 'Departamento de Física', 'docente' => 'Profesor Titular', 'cientifica' => 'Investigador Titular', 'grado' => 'Doctor en Ciencias'],
            ['nombre' => 'MSc. Adriana Suárez Mora', 'email' => 'adriana.suarez@universidad.test', 'cedula' => 'DOC-0005', 'departamento' => 'Departamento de Ciencias de la Información', 'docente' => 'Profesor Auxiliar', 'cientifica' => 'Investigador Agregado', 'grado' => 'Máster en Ciencias'],
            ['nombre' => 'Dra. Isabel Torres Núñez', 'email' => 'isabel.torres@universidad.test', 'cedula' => 'DOC-0006', 'departamento' => 'Departamento de Letras', 'docente' => 'Profesor Titular', 'cientifica' => 'Investigador Auxiliar', 'grado' => 'Doctor en Ciencias'],
            ['nombre' => 'MSc. Roberto Medina Cruz', 'email' => 'roberto.medina@universidad.test', 'cedula' => 'DOC-0007', 'departamento' => 'Departamento de Lengua Inglesa', 'docente' => 'Profesor Auxiliar', 'cientifica' => 'Investigador Agregado', 'grado' => 'Máster en Ciencias'],
        ];
        $professorsByDepartment = [];

        foreach ($professors as $professorData) {
            $department = DB::table('departamento')->where('nombre', $professorData['departamento'])->firstOrFail();
            $professor = Profesor::query()->updateOrCreate(
                ['cedula' => $professorData['cedula']],
                ['nombre' => $professorData['nombre'], 'email' => $professorData['email']],
            );

            DB::table('miembro_departamento')->updateOrInsert([
                'profesor_id' => $professor->id,
                'departamento_id' => $department->id,
            ], $this->timestamps());

            DB::table('profesor_categoria_grado')->updateOrInsert([
                'profesor_id' => $professor->id,
                'categoria_docente_id' => DB::table('categoria_docente')->where('nombre', $professorData['docente'])->value('id'),
                'categoria_cientifica_id' => DB::table('categoria_cientifica')->where('nombre', $professorData['cientifica'])->value('id'),
                'grado_titulo_id' => DB::table('grado_titulo')->where('nombre', $professorData['grado'])->value('id'),
            ], $this->timestamps());

            if (! isset($professorsByDepartment[$department->id])) {
                DB::table('jefe_departamento')->updateOrInsert(
                    ['departamento_id' => $department->id, 'fecha_fin' => null],
                    ['profesor_id' => $professor->id, 'fecha_inicio' => '2025-09-01 00:00:00', ...$this->timestamps()],
                );
            }

            $professorsByDepartment[$department->id][] = $professor->id;
        }

        return $professorsByDepartment;
    }

    /**
     * @param  array<int, list<int>>  $professorsByDepartment
     * @return array<int, list<int>>
     */
    private function seedTeachingAssignments(array $professorsByDepartment): array
    {
        $course = CursoAcademico::query()->where('activo', true)->firstOrFail();
        $assignmentsByCareer = [];
        $subjectCounters = [];
        $subjects = DB::table('asignatura')
            ->join('disciplina', 'disciplina.id', '=', 'asignatura.disciplina_id')
            ->join('plan_estudio', 'plan_estudio.id', '=', 'disciplina.plan_estudio_id')
            ->join('carrera_departamento', 'carrera_departamento.carrera_id', '=', 'plan_estudio.carrera_id')
            ->select(['asignatura.id as asignatura_id', 'plan_estudio.carrera_id', 'carrera_departamento.departamento_id'])
            ->orderBy('asignatura.codigo')
            ->get();

        foreach ($subjects as $subject) {
            $departmentProfessors = $professorsByDepartment[$subject->departamento_id];
            $counter = $subjectCounters[$subject->departamento_id] ?? 0;
            $professorId = $departmentProfessors[$counter % count($departmentProfessors)];
            $subjectCounters[$subject->departamento_id] = $counter + 1;

            $assignment = ProfesorAsignatura::query()->updateOrCreate(
                [
                    'profesor_id' => $professorId,
                    'asignatura_id' => $subject->asignatura_id,
                    'curso_academico_id' => $course->id,
                ],
                [],
            );

            $assignmentsByCareer[$subject->carrera_id][] = $assignment->id;
        }

        return $assignmentsByCareer;
    }

    /**
     * @param  array<int, list<int>>  $assignmentsByCareer
     */
    private function seedStudents(array $assignmentsByCareer): void
    {
        $students = [
            ['nombre' => 'Ana Lucía Pérez Gómez', 'email' => 'ana.perez@estudiantes.test', 'carrera' => 'Ingeniería Informática', 'nivel' => 1],
            ['nombre' => 'Carlos Manuel Ruiz Díaz', 'email' => 'carlos.ruiz@estudiantes.test', 'carrera' => 'Ingeniería Informática', 'nivel' => 2],
            ['nombre' => 'Daniela Romero León', 'email' => 'daniela.romero@estudiantes.test', 'carrera' => 'Ciencias de la Computación', 'nivel' => 1],
            ['nombre' => 'Jorge Luis Herrera Soto', 'email' => 'jorge.herrera@estudiantes.test', 'carrera' => 'Ciencias de la Computación', 'nivel' => 2],
            ['nombre' => 'María Fernanda López Cruz', 'email' => 'maria.lopez@estudiantes.test', 'carrera' => 'Matemática', 'nivel' => 1],
            ['nombre' => 'Alejandro Vega Ramos', 'email' => 'alejandro.vega@estudiantes.test', 'carrera' => 'Matemática', 'nivel' => 2],
            ['nombre' => 'Lucía Gómez Arias', 'email' => 'lucia.gomez@estudiantes.test', 'carrera' => 'Física', 'nivel' => 1],
            ['nombre' => 'Samuel Castillo Peña', 'email' => 'samuel.castillo@estudiantes.test', 'carrera' => 'Física', 'nivel' => 2],
            ['nombre' => 'Camila Rodríguez Gil', 'email' => 'camila.rodriguez@estudiantes.test', 'carrera' => 'Ciencias de la Información', 'nivel' => 1],
            ['nombre' => 'Diego Morales Fuentes', 'email' => 'diego.morales@estudiantes.test', 'carrera' => 'Ciencias de la Información', 'nivel' => 2],
            ['nombre' => 'Valeria Sánchez Ortiz', 'email' => 'valeria.sanchez@estudiantes.test', 'carrera' => 'Letras', 'nivel' => 1],
            ['nombre' => 'Pablo Núñez Cabrera', 'email' => 'pablo.nunez@estudiantes.test', 'carrera' => 'Letras', 'nivel' => 2],
            ['nombre' => 'Gabriela Martín Reyes', 'email' => 'gabriela.martin@estudiantes.test', 'carrera' => 'Lengua Inglesa', 'nivel' => 1],
            ['nombre' => 'Andrés Silva Acosta', 'email' => 'andres.silva@estudiantes.test', 'carrera' => 'Lengua Inglesa', 'nivel' => 2],
        ];
        $course = CursoAcademico::query()->where('activo', true)->firstOrFail();

        foreach ($students as $index => $studentData) {
            $career = DB::table('carrera')->where('nombre', $studentData['carrera'])->firstOrFail();
            $academicYear = DB::table('anio_academico')
                ->where('carrera_id', $career->id)
                ->where('nivel', $studentData['nivel'])
                ->firstOrFail();
            $groupId = DB::table('grupo')
                ->where('anio_academico_id', $academicYear->id)
                ->orderBy('nombre')
                ->value('id');
            $student = Alumno::query()->updateOrCreate(
                ['identificador_institucional' => sprintf('EST-2026-%04d', $index + 1)],
                [
                    'nombre' => $studentData['nombre'],
                    'email' => $studentData['email'],
                    'grupo_id' => $groupId,
                    'carrera' => $studentData['carrera'],
                ],
            );

            DB::table('evaluacion_integral')->updateOrInsert([
                'alumno_id' => $student->id,
                'anio_academico_id' => $academicYear->id,
            ], [
                'evaluacion_docencia' => 'MB',
                'investigacion' => $index % 2 === 0 ? 'E' : 'MB',
                'comportamiento_social' => 'E',
                'actividades_extension' => 'MB',
                'formacion_politica' => 'B',
                'tareas_impacto' => 'MB',
                ...$this->timestamps(),
            ]);

            if ($index % 2 === 0) {
                $assignment = ProfesorAsignatura::query()->findOrFail($assignmentsByCareer[$career->id][0]);
                AlumnoAyudante::query()->updateOrCreate(
                    ['alumno_id' => $student->id, 'profesor_asignatura_id' => $assignment->id],
                    ['curso_academico_id' => $course->id],
                );
            }
        }

        $withdrawnStudent = Alumno::query()->where('identificador_institucional', 'EST-2026-0014')->firstOrFail();
        DB::table('baja_estudiante')->updateOrInsert(
            ['alumno_id' => $withdrawnStudent->id],
            ['causa' => 'voluntaria', 'observaciones' => 'Solicitud de baja voluntaria por cambio de residencia.', ...$this->timestamps()],
        );
    }

    /**
     * @return array{created_at: Carbon, updated_at: Carbon}
     */
    private function timestamps(): array
    {
        return ['created_at' => now(), 'updated_at' => now()];
    }
}
