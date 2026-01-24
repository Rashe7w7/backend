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
        // This constraint doesn't exist in the current schema, so we skip it
        // Schema::table('indicadores', function (Blueprint $table) {
        //     $table->dropUnique('indicadores_nombre_key');
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('indicadores', function (Blueprint $table) {
            $table->unique(['nombre']);
        });
    }
};
