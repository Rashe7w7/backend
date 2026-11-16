<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('anio_asignatura')) {
            Schema::create('anio_asignatura', function (Blueprint $table) {
                $table->id();
                $table->foreignId('anio_academico_id')->constrained('anio_academico')->cascadeOnDelete();
                $table->foreignId('asignatura_id')->constrained('asignatura')->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['anio_academico_id', 'asignatura_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('anio_asignatura');
    }
};
