<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('indicador_anno')) {
            return;
        }

        if (! Schema::hasColumn('indicador_anno', 'nombre')) {
            Schema::table('indicador_anno', function (Blueprint $table) {
                $table->string('nombre')->nullable()->after('id');
            });
        }

        DB::table('indicador_anno')
            ->whereNull('nombre')
            ->update([
                'nombre' => DB::raw('(select nombre from indicador where indicador.id = indicador_anno.indicador_id)'),
            ]);

        Schema::table('indicador_anno', function (Blueprint $table) {
            $table->foreignId('anio_academico_id')->nullable()->change();
            $table->foreignId('curso_academico_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Keep the compatible schema on rollback; restoring NOT NULL would lose valid rows.
    }
};
