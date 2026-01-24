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
        Schema::create('indicador_valores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indicador_id')->constrained('indicador')->cascadeOnDelete();
            $table->decimal('valor', 10, 2)->nullable();
            $table->timestamps();
            $table->index('indicador_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indicador_valores');
    }
};
