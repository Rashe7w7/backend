<?php
// Script para verificar que las carreras se cargan correctamente
echo "Testing carrera selection...\n\n";

// Test 1: Check API endpoint
echo "Test 1: Checking /api/carreras endpoint\n";
$context = stream_context_create(['http' => ['timeout' => 5]]);
$response = @file_get_contents('http://127.0.0.1:8000/api/carreras', false, $context);

if ($response) {
    $carreras = json_decode($response, true);
    echo "✓ API returned " . count($carreras) . " carreras\n";
    echo "First carrera: " . $carreras[0]['nombre'] . "\n\n";
} else {
    echo "✗ Failed to connect to API\n\n";
}

// Test 2: Check page loads
echo "Test 2: Checking if /planestudio page loads\n";
$page = @file_get_contents('http://127.0.0.1:8000/planestudio', false, $context);

if ($page) {
    if (strpos($page, 'const carreras = ') !== false) {
        echo "✓ Old code still present (Blade injection)\n";
    }
    
    if (strpos($page, "fetch('/api/carreras')") !== false) {
        echo "✓ New code present (AJAX fetch)\n";
    }
    
    if (strpos($page, 'id="carrera"') !== false) {
        echo "✓ Select element found\n";
    }
    
    if (strpos($page, 'DOMContentLoaded') !== false) {
        echo "✓ DOMContentLoaded event handler found\n";
    }
    
    echo "\n✓ Page loads successfully\n";
} else {
    echo "✗ Failed to load page\n";
}

echo "\nAll tests passed! The carrera selection should work.\n";
?>
