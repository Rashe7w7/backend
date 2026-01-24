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
        Schema::table('indicador_carrera', function (Blueprint $table) {
            $table->foreignId('carrera_id')->nullable()->change();
            $table->foreignId('curso_academico_id')->nullable()->change();
        });

        Schema::table('indicador_grupo', function (Blueprint $table) {
            $table->foreignId('grupo_id')->nullable()->change();
            $table->foreignId('curso_academico_id')->nullable()->change();
        });

        Schema::table('indicador_facultad', function (Blueprint $table) {
            $table->foreignId('facultad_id')->nullable()->change();
            $table->foreignId('curso_academico_id')->nullable()->change();
        });

        Schema::table('indicador_departamento', function (Blueprint $table) {
            $table->foreignId('departamento_id')->nullable()->change();
            $table->foreignId('curso_academico_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('indicador_carrera', function (Blueprint $table) {
            $table->foreignId('carrera_id')->nullable(false)->change();
            $table->foreignId('curso_academico_id')->nullable(false)->change();
        });

        Schema::table('indicador_grupo', function (Blueprint $table) {
            $table->foreignId('grupo_id')->nullable(false)->change();
            $table->foreignId('curso_academico_id')->nullable(false)->change();
        });

        Schema::table('indicador_facultad', function (Blueprint $table) {
            $table->foreignId('facultad_id')->nullable(false)->change();
            $table->foreignId('curso_academico_id')->nullable(false)->change();
        });

        Schema::table('indicador_departamento', function (Blueprint $table) {
            $table->foreignId('departamento_id')->nullable(false)->change();
            $table->foreignId('curso_academico_id')->nullable(false)->change();
        });
    }
};
