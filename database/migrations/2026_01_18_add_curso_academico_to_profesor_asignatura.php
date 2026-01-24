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
        Schema::table('profesor_asignatura', function (Blueprint $table) {
            $table->foreignId('curso_academico_id')->nullable()->constrained('curso_academico')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profesor_asignatura', function (Blueprint $table) {
            $table->dropForeign(['curso_academico_id']);
            $table->dropColumn('curso_academico_id');
        });
    }
};
