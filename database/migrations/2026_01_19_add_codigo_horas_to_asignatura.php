<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asignatura', function (Blueprint $table) {
            // Agregar columnas si no existen
            if (!Schema::hasColumn('asignatura', 'codigo')) {
                $table->string('codigo')->nullable()->unique();
            }
            if (!Schema::hasColumn('asignatura', 'horas')) {
                $table->integer('horas')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('asignatura', function (Blueprint $table) {
            $table->dropColumn(['codigo', 'horas']);
        });
    }
};
