<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Rename indicador_valores to indicador_valor (singular)
     */
    public function up(): void
    {
        if (Schema::hasTable('indicador_valores')) {
            Schema::rename('indicador_valores', 'indicador_valor');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('indicador_valor')) {
            Schema::rename('indicador_valor', 'indicador_valores');
        }
    }
};
