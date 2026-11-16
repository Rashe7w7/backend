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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        $timestamp = now();
        DB::table('roles')->insert(collect([
            'administrador' => 'Administrador',
            'ppa' => 'PPA',
            'jefe_departamento' => 'Jefe de Departamento',
            'rector' => 'Rector',
            'vicerrector' => 'Vicerrector',
            'decano' => 'Decano',
            'secretaria' => 'Secretaria',
        ])->map(fn (string $nombre, string $slug) => [
            'nombre' => $nombre,
            'slug' => $slug,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ])->values()->all());

        Schema::create('role_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['role_id', 'user_id']);
        });

        Schema::table('carrera', function (Blueprint $table) {
            $table->unsignedTinyInteger('duracion_anios')->nullable();
        });

        Schema::table('anio_academico', function (Blueprint $table) {
            $table->dropUnique('anio_academico_nombre_unique');
            $table->unsignedTinyInteger('nivel')->nullable();
        });

        DB::table('anio_academico')->orderBy('id')->get()->each(function (object $anio): void {
            preg_match('/[1-5]/', (string) $anio->nombre, $matches);

            if ($matches === []) {
                return;
            }

            $alreadyUsed = DB::table('anio_academico')
                ->where('carrera_id', $anio->carrera_id)
                ->where('nivel', (int) $matches[0])
                ->exists();

            if (! $alreadyUsed) {
                DB::table('anio_academico')->where('id', $anio->id)->update(['nivel' => (int) $matches[0]]);
            }
        });

        Schema::table('anio_academico', function (Blueprint $table) {
            $table->unique(['carrera_id', 'nivel']);
        });

        Schema::table('grupo', function (Blueprint $table) {
            $table->foreignId('curso_academico_id')->nullable()->constrained('curso_academico')->restrictOnDelete();
        });

        Schema::create('asignacion_ppa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('grupo_id')->constrained('grupo')->restrictOnDelete();
            $table->foreignId('curso_academico_id')->constrained('curso_academico')->restrictOnDelete();
            $table->string('resolucion_decanal')->nullable();
            $table->date('fecha_designacion');
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->unique(['user_id', 'grupo_id', 'curso_academico_id']);
        });

        Schema::create('alcance_usuario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('ambito_tipo', 32);
            $table->unsignedBigInteger('ambito_id');
            $table->boolean('puede_exportar')->default(false);
            $table->timestamps();
            $table->unique(['user_id', 'ambito_tipo', 'ambito_id']);
            $table->index(['ambito_tipo', 'ambito_id']);
        });

        Schema::table('estudiante_ayudante', function (Blueprint $table) {
            $table->foreignId('grupo_id')->nullable()->constrained('grupo')->restrictOnDelete();
            $table->foreignId('asignacion_ppa_id')->nullable()->constrained('asignacion_ppa')->nullOnDelete();
            $table->date('fecha_designacion')->nullable();
            $table->string('estado', 16)->default('activo');
            $table->index(['alumno_id', 'curso_academico_id', 'estado']);
        });

        Schema::create('auditoria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('accion', 64);
            $table->string('entidad', 128);
            $table->string('entidad_id')->nullable();
            $table->json('datos_anteriores')->nullable();
            $table->json('datos_nuevos')->nullable();
            $table->string('resultado', 32)->default('exitoso');
            $table->ipAddress('ip')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['accion', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auditoria');

        Schema::table('estudiante_ayudante', function (Blueprint $table) {
            $table->dropConstrainedForeignId('asignacion_ppa_id');
            $table->dropConstrainedForeignId('grupo_id');
            $table->dropColumn(['fecha_designacion', 'estado']);
        });

        Schema::dropIfExists('alcance_usuario');
        Schema::dropIfExists('asignacion_ppa');

        Schema::table('grupo', function (Blueprint $table) {
            $table->dropConstrainedForeignId('curso_academico_id');
        });

        Schema::table('anio_academico', function (Blueprint $table) {
            $table->dropUnique(['carrera_id', 'nivel']);
            $table->dropColumn('nivel');
            $table->unique('nombre');
        });

        Schema::table('carrera', function (Blueprint $table) {
            $table->dropColumn('duracion_anios');
        });

        Schema::dropIfExists('role_user');
        Schema::dropIfExists('roles');
    }
};
