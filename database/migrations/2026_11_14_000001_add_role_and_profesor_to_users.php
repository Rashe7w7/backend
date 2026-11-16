<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 32)->default('consulta')->index();
            $table->foreignId('profesor_id')->nullable()->unique()->constrained('profesor')->nullOnDelete();
        });

        DB::table('users')->where('email', env('APP_ADMIN_EMAIL', 'admin@example.com'))
            ->update(['role' => 'administrador']);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('profesor_id');
            $table->dropColumn('role');
        });
    }
};
