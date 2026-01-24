<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Facultades
        Schema::create('facultad', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->timestamps();
        });

        // Departamentos
        Schema::create('departamento', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->foreignId('facultad_id')->constrained('facultad')->cascadeOnDelete();
            $table->timestamps();
            $table->index('facultad_id');
        });

        // Carreras
        Schema::create('carrera', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->foreignId('facultad_id')->constrained('facultad')->cascadeOnDelete();
            $table->timestamps();
            $table->index('facultad_id');
        });

        // Planes de estudio
        Schema::create('plan_estudio', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->foreignId('carrera_id')->constrained('carrera')->cascadeOnDelete();
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->boolean('vigente')->default(true);
            $table->timestamps();
            $table->index('carrera_id');
        });

        // Disciplinas
        Schema::create('disciplina', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->foreignId('plan_estudio_id')->constrained('plan_estudio')->cascadeOnDelete();
            $table->timestamps();
            $table->index('plan_estudio_id');
        });

        // Asignaturas
        Schema::create('asignatura', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->foreignId('disciplina_id')->constrained('disciplina')->cascadeOnDelete();
            $table->timestamps();
            $table->index('disciplina_id');
        });

        // Currículos
        Schema::create('curriculo', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->foreignId('asignatura_id')->constrained('asignatura')->cascadeOnDelete();
            $table->timestamps();
            $table->index('asignatura_id');
        });

        // Años académicos
        Schema::create('anio_academico', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->foreignId('carrera_id')->constrained('carrera')->cascadeOnDelete();
            $table->timestamps();
            $table->index('carrera_id');
        });

        // Grupos
        Schema::create('grupo', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->foreignId('anio_academico_id')->constrained('anio_academico')->cascadeOnDelete();
            $table->timestamps();
            $table->index('anio_academico_id');
        });

        // Alumnos
        Schema::create('estudiante', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('email')->unique()->nullable();
            $table->foreignId('grupo_id')->constrained('grupo')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->index('grupo_id');
        });

        // Categorías de indicadores
        Schema::create('categoria_indicador', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->timestamps();
        });

        // Indicadores
        Schema::create('indicador', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->enum('fuente', ['baja_estudiante', 'evaluacion_integral']);
            $table->foreignId('categoria_indicador_id')->constrained('categoria_indicador')->cascadeOnDelete();
            $table->timestamps();
            $table->index('categoria_indicador_id');
        });

        // Aplicaciones de indicadores
        Schema::create('indicador_aplicacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indicador_id')->constrained('indicador')->cascadeOnDelete();
            $table->enum('ambito_tipo', [
                'facultad',
                'carrera',
                'plan_estudio',
                'disciplina',
                'asignatura',
                'anio_academico'
            ]);
            $table->unsignedBigInteger('ambito_id');
            $table->timestamps();
            $table->index('indicador_id');
            $table->index(['ambito_tipo', 'ambito_id']);
        });

        // Resultados de indicadores
        Schema::create('indicador_resultado', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indicador_id')->constrained('indicador')->cascadeOnDelete();
            $table->enum('ambito_tipo', [
                'facultad',
                'carrera',
                'plan_estudio',
                'disciplina',
                'asignatura',
                'anio_academico'
            ]);
            $table->unsignedBigInteger('ambito_id');
            $table->string('periodo'); // Ej: 2023-2024
            $table->decimal('valor', 8, 2);
            $table->timestamps();
            $table->index('indicador_id');
            $table->index(['ambito_tipo', 'ambito_id']);
        });

        // Evaluaciones integrales
        Schema::create('evaluacion_integral', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumno_id')->constrained('estudiante')->cascadeOnDelete();
            $table->foreignId('anio_academico_id')->constrained('anio_academico')->cascadeOnDelete();

            $table->enum('evaluacion_docencia', ['E','MB','B','R','M'])->default('R');
            $table->enum('investigacion', ['E','MB','B','R','M'])->default('R');
            $table->enum('comportamiento_social', ['E','MB','B','R','M'])->default('R');
            $table->enum('residencia_estudiantil', ['E','MB','B','R','M'])->nullable();
            $table->enum('actividades_extension', ['E','MB','B','R','M'])->default('R');
            $table->enum('defensa', ['E','MB','B','R','M'])->nullable();
            $table->enum('formacion_politica', ['E','MB','B','R','M'])->default('R');
            $table->enum('tareas_impacto', ['E','MB','B','R','M'])->default('R');
            
            $table->timestamps();
            $table->index('alumno_id');
            $table->index('anio_academico_id');
        });

        // Bajas de estudiantes
        Schema::create('baja_estudiante', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumno_id')->constrained('estudiante')->cascadeOnDelete();
            $table->enum('causa', [
                'insuficiencia_docente',
                'sancion_disciplinaria',
                'voluntaria',
                'inasistencia',
                'desercion',
                'definitiva',
                'otras_causas'
            ]);
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->index('alumno_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('baja_estudiante');
        Schema::dropIfExists('evaluacion_integral');
        Schema::dropIfExists('indicador_resultado');
        Schema::dropIfExists('indicador_aplicacion');
        Schema::dropIfExists('indicador');
        Schema::dropIfExists('categoria_indicador');
        Schema::dropIfExists('estudiante');
        Schema::dropIfExists('grupo');
        Schema::dropIfExists('anio_academico');
        Schema::dropIfExists('curriculo');
        Schema::dropIfExists('asignatura');
        Schema::dropIfExists('disciplina');
        Schema::dropIfExists('plan_estudio');
        Schema::dropIfExists('carrera');
        Schema::dropIfExists('departamento');
        Schema::dropIfExists('facultad');
    }
    
};
