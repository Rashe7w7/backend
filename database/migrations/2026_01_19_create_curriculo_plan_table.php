<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('curriculo_plan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curriculo_id')->constrained('curriculo')->cascadeOnDelete();
            $table->foreignId('plan_estudio_id')->constrained('plan_estudio')->cascadeOnDelete();
            $table->timestamps();
            
            // Índice único para evitar duplicados
            $table->unique(['curriculo_id', 'plan_estudio_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('curriculo_plan');
    }
};
