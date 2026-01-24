<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class TablesController extends Controller
{
    public function index()
    {
        return view('welcome');
    }

    public function showTables()
    {
        $database = config('database.connections.' . config('database.default') . '.database');
        
        // Obtener todas las tablas
        $tables = Schema::getTables();
        $tableNames = array_column($tables, 'name');
        
        // Filtrar solo las tablas de nuestro esquema
        $appTables = array_filter($tableNames, function($table) {
            return !in_array($table, ['cache', 'sessions', 'job_batches', 'jobs', 'failed_jobs', 'password_reset_tokens']);
        });
        
        $tablesData = [];
        
        foreach ($appTables as $table) {
            $columns = Schema::getColumns($table);
            $count = DB::table($table)->count();
            
            $tablesData[] = [
                'name' => $table,
                'columns' => $columns,
                'count' => $count,
                'data' => DB::table($table)->limit(5)->get()
            ];
        }
        
        return view('tables.index', compact('tablesData'));
    }
}
