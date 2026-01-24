<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Remove especificacion column from indicador table as it's redundant.
     * The table hierarchy already defines the type of indicador through the child tables
     */
    public function up(): void
    {
        if (Schema::hasColumn('indicador', 'especificacion')) {
            Schema::table('indicador', function (Blueprint $table) {
                $table->dropColumn('especificacion');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('indicador', function (Blueprint $table) {
            $table->enum('especificacion', ['grupo', 'facultad', 'departamento', 'carrera', 'anno', 'general'])
                ->default('general')
                ->after('categoria_indicador_id');
        });
    }
};
