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
        Schema::table('indicador', function (Blueprint $table) {
            $table->enum('especificacion', ['grupo', 'facultad', 'departamento', 'carrera', 'anno', 'general'])->default('general')->after('categoria_indicador_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('indicador', function (Blueprint $table) {
            $table->dropColumn('especificacion');
        });
    }
};
