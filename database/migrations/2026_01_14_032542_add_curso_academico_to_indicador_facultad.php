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
        Schema::table('indicador_facultad', function (Blueprint $table) {
            // Agregar como nullable primero
            if (!Schema::hasColumn('indicador_facultad', 'curso_academico_id')) {
                $table->foreignId('curso_academico_id')->nullable()->constrained('curso_academico')->cascadeOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('indicador_facultad', function (Blueprint $table) {
            // Simplemente dropear la columna, la foreign key se dropea con ella
            $table->dropColumn('curso_academico_id');
        });
    }
};
