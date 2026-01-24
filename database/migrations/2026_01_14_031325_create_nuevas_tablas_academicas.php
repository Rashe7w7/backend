<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Profesores
        Schema::create('profesor', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('email')->unique()->nullable();
            $table->string('cedula')->unique()->nullable();
            $table->timestamps();
        });

        // Categorías Docentes
        Schema::create('categoria_docente', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->timestamps();
        });

        // Categorías Científicas
        Schema::create('categoria_cientifica', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->timestamps();
        });

        // Grados de Título
        Schema::create('grado_titulo', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->timestamps();
        });

        // Profesor - Categoría - Grado (relación muchos a muchos)
        Schema::create('profesor_categoria_grado', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profesor_id')->constrained('profesor')->cascadeOnDelete();
            $table->foreignId('categoria_docente_id')->constrained('categoria_docente')->cascadeOnDelete();
            $table->foreignId('categoria_cientifica_id')->constrained('categoria_cientifica')->cascadeOnDelete();
            $table->foreignId('grado_titulo_id')->constrained('grado_titulo')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['profesor_id', 'categoria_docente_id', 'categoria_cientifica_id', 'grado_titulo_id']);
        });

        // Jefe de Departamento
        Schema::create('jefe_departamento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profesor_id')->constrained('profesor')->cascadeOnDelete();
            $table->foreignId('departamento_id')->constrained('departamento')->cascadeOnDelete();
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_fin')->nullable();
            $table->timestamps();
        });

        // Miembro de Departamento
        Schema::create('miembro_departamento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profesor_id')->constrained('profesor')->cascadeOnDelete();
            $table->foreignId('departamento_id')->constrained('departamento')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['profesor_id', 'departamento_id']);
        });

        // Estudiante Ayudante (cambiar alumno_ayudante a estudiante_ayudante)
        Schema::create('estudiante_ayudante', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumno_id')->constrained('estudiante')->cascadeOnDelete();
            $table->foreignId('asignatura_id')->constrained('asignatura')->cascadeOnDelete();
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_fin')->nullable();
            $table->timestamps();
        });

        // Facultad - Departamento
        Schema::create('facultad_departamento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facultad_id')->constrained('facultad')->cascadeOnDelete();
            $table->foreignId('departamento_id')->constrained('departamento')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['facultad_id', 'departamento_id']);
        });

        // Carrera - Departamento
        Schema::create('carrera_departamento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carrera_id')->constrained('carrera')->cascadeOnDelete();
            $table->foreignId('departamento_id')->constrained('departamento')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['carrera_id', 'departamento_id']);
        });

        // Carrera - Plan de Estudio
        Schema::create('carrera_plan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carrera_id')->constrained('carrera')->cascadeOnDelete();
            $table->foreignId('plan_estudio_id')->constrained('plan_estudio')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['carrera_id', 'plan_estudio_id']);
        });

        // Curso Académico (tabla maestra)
        Schema::create('curso_academico', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_fin');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // Versiones (para control de versiones de datos)
        Schema::create('version', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('descripcion')->nullable();
            $table->unsignedBigInteger('creada_por')->nullable();
            $table->timestamps();
        });

        // Modificaciones (registro de cambios)
        Schema::create('modificacion', function (Blueprint $table) {
            $table->id();
            $table->string('tabla');
            $table->unsignedBigInteger('registro_id');
            $table->string('tipo'); // create, update, delete
            $table->longText('datos_anteriores')->nullable();
            $table->longText('datos_nuevos')->nullable();
            $table->unsignedBigInteger('usuario_id')->nullable();
            $table->timestamps();
            $table->index(['tabla', 'registro_id']);
        });

        // Versión - Modificaciones (relación muchos a muchos)
        Schema::create('version_modificacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('version_id')->constrained('version')->cascadeOnDelete();
            $table->foreignId('modificacion_id')->constrained('modificacion')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['version_id', 'modificacion_id']);
        });

        // Indicador - Categoría
        Schema::create('indicador_categoria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indicador_id')->constrained('indicador')->cascadeOnDelete();
            $table->foreignId('categoria_indicador_id')->constrained('categoria_indicador')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['indicador_id', 'categoria_indicador_id']);
        });

        // Indicador - Curso Académico
        Schema::create('indicador_curso', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indicador_id')->constrained('indicador')->cascadeOnDelete();
            $table->foreignId('curso_academico_id')->constrained('curso_academico')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['indicador_id', 'curso_academico_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indicador_curso');
        Schema::dropIfExists('indicador_categoria');
        Schema::dropIfExists('version_modificacion');
        Schema::dropIfExists('modificacion');
        Schema::dropIfExists('version');
        Schema::dropIfExists('curso_academico');
        Schema::dropIfExists('carrera_plan');
        Schema::dropIfExists('carrera_departamento');
        Schema::dropIfExists('facultad_departamento');
        Schema::dropIfExists('estudiante_ayudante');
        Schema::dropIfExists('miembro_departamento');
        Schema::dropIfExists('jefe_departamento');
        Schema::dropIfExists('profesor_categoria_grado');
        Schema::dropIfExists('grado_titulo');
        Schema::dropIfExists('categoria_cientifica');
        Schema::dropIfExists('categoria_docente');
        Schema::dropIfExists('profesor');
    }
};
