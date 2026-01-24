<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Rename tables to singular form and change alumno/alumnos to estudiante
     */
    public function up(): void
    {
        // Migration is now obsolete - all tables created in singular form from the start
        // This migration is a no-op
    }

    public function down(): void
    {
        // No-op - migration is obsolete
    }
};
