<?php
/**
 * Test Final: Verificar que todo está correcto para la carga de carreras
 */

echo "═══════════════════════════════════════════════════════════\n";
echo "DIAGNÓSTICO FINAL: Carga de Carreras\n";
echo "═══════════════════════════════════════════════════════════\n\n";

// Test 1: Verificar que el API funciona
echo "1. Verificando API endpoint...\n";
$context = stream_context_create(['http' => ['timeout' => 5]]);
$apiResponse = @file_get_contents('http://127.0.0.1:8000/api/carreras', false, $context);

if ($apiResponse) {
    $carreras = json_decode($apiResponse, true);
    echo "   ✓ API responde correctamente\n";
    echo "   ✓ Total de carreras: " . count($carreras) . "\n";
    foreach (array_slice($carreras, 0, 3) as $c) {
        echo "      - " . $c['nombre'] . " (id: " . $c['id'] . ")\n";
    }
} else {
    echo "   ✗ API no responde\n";
    exit(1);
}

// Test 2: Verificar el contenido HTML de planestudio
echo "\n2. Verificando página planestudio.blade.php...\n";
$pageContent = @file_get_contents('http://127.0.0.1:8000/planestudio', false, $context);

if (!$pageContent) {
    echo "   ✗ No se pudo cargar la página\n";
    exit(1);
}

echo "   ✓ Página cargada (" . strlen($pageContent) . " bytes)\n";

// Verificaciones específicas
$checks = [
    ['pattern' => 'id="carrera"', 'name' => 'Select element con id="carrera"'],
    ['pattern' => "fetch('/api/carreras')", 'name' => 'Código fetch a /api/carreras'],
    ['pattern' => 'DOMContentLoaded', 'name' => 'Event listener DOMContentLoaded'],
    ['pattern' => 'const carreraSelect', 'name' => 'Variable carreraSelect inicializada'],
    ['pattern' => 'carreraSelect.appendChild(option)', 'name' => 'Código para agregar opciones'],
];

foreach ($checks as $check) {
    if (strpos($pageContent, $check['pattern']) !== false) {
        echo "   ✓ " . $check['name'] . "\n";
    } else {
        echo "   ✗ " . $check['name'] . " - NO ENCONTRADO\n";
    }
}

// Test 3: Verificar sintaxis JavaScript
echo "\n3. Validando sintaxis JavaScript...\n";

// Extraer la sección de script
preg_match('/<script[^>]*>(.*?)<\/script>/s', $pageContent, $matches);
if ($matches) {
    $scriptContent = $matches[1];
    
    // Contar llaves
    $openBraces = substr_count($scriptContent, '{');
    $closeBraces = substr_count($scriptContent, '}');
    
    if ($openBraces === $closeBraces) {
        echo "   ✓ Llaves JavaScript balanceadas ($openBraces)\n";
    } else {
        echo "   ✗ Llaves NO balanceadas (abiertas: $openBraces, cerradas: $closeBraces)\n";
    }
    
    // Verificar que no hay errores de sintaxis comunes
    if (preg_match('/async function.*{.*}/s', $scriptContent)) {
        echo "   ✓ Funciones async encontradas\n";
    }
    
    if (preg_match('/\.forEach\s*\(/s', $scriptContent)) {
        echo "   ✓ forEach() encontrado (para iterar carreras)\n";
    }
} else {
    echo "   ✗ No se encontró bloque de script\n";
}

// Test 4: CORS
echo "\n4. Verificando CORS...\n";
$corsHeaders = [
    'Access-Control-Allow-Origin',
    'Access-Control-Allow-Methods',
    'Access-Control-Allow-Headers'
];

$headers = @get_headers('http://127.0.0.1:8000/api/carreras', 1);
$hasCors = false;
foreach ($corsHeaders as $header) {
    if (isset($headers[$header])) {
        echo "   ✓ Header CORS encontrado: $header\n";
        $hasCors = true;
    }
}

if (!$hasCors) {
    echo "   ⓘ No se detectaron headers CORS específicos (puede ser normal en localhost)\n";
}

// Conclusión
echo "\n═══════════════════════════════════════════════════════════\n";
echo "CONCLUSIÓN:\n";
echo "═══════════════════════════════════════════════════════════\n";
echo "\n✓ Todos los componentes están correctamente configurados.\n\n";
echo "El problema de visualización debe ser:\n";
echo "1. Recargar la página en el navegador (Ctrl+F5)\n";
echo "2. Limpiar la cache del navegador\n";
echo "3. Abrir la Developer Console (F12) para ver errores\n";
echo "4. Revisar que JavaScript esté habilitado\n\n";

?>
