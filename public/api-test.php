<?php
// Simple test endpoint

require 'bootstrap/app.php';

// Don't output anything - just test

$response = file_get_contents('php://input');

if ($_SERVER['REQUEST_URI'] === '/api/carreras') {
    header('Content-Type: application/json');
    try {
        $app = require_once 'bootstrap/app.php';
        $app->make('Illuminate\Contracts\Http\Kernel')->handle(
            $request = Illuminate\Http\Request::capture()
        );
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}
?>
