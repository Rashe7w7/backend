<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$pdo = DB::connection()->getPdo();

echo "=== TABLA estudiante_ayudante ===\n";
$result = $pdo->query("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = 'estudiante_ayudante' ORDER BY ordinal_position");
foreach ($result as $row) {
    echo $row['column_name'] . " - " . $row['data_type'] . "\n";
}

echo "\n=== TABLA profesor_asignatura ===\n";
$result = $pdo->query("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = 'profesor_asignatura' ORDER BY ordinal_position");
foreach ($result as $row) {
    echo $row['column_name'] . " - " . $row['data_type'] . "\n";
}
