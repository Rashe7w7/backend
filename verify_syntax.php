<?php

$content = file_get_contents('resources/views/tables/planestudio.blade.php');

// Buscar la sección de DOMContentLoaded
$pattern = '/document\.addEventListener.*?DOMContentLoaded/s';
if (preg_match($pattern, $content)) {
    echo "✓ DOMContentLoaded encontrado\n";
} else {
    echo "✗ DOMContentLoaded NO encontrado\n";
}

// Contar los abiertos y cerrados de llaves en la sección
$scriptStart = strpos($content, 'document.addEventListener');
$scriptEnd = strrpos($content, '</script>');

if ($scriptStart && $scriptEnd) {
    $scriptSection = substr($content, $scriptStart, $scriptEnd - $scriptStart);
    
    $openBraces = substr_count($scriptSection, '{');
    $closeBraces = substr_count($scriptSection, '}');
    
    echo "Llaves abiertas: " . $openBraces . "\n";
    echo "Llaves cerradas: " . $closeBraces . "\n";
    
    if ($openBraces === $closeBraces) {
        echo "✓ Las llaves están balanceadas\n";
    } else {
        echo "✗ Las llaves NO están balanceadas\n";
        echo "Diferencia: " . ($openBraces - $closeBraces) . "\n";
    }
}

// Buscar específicamente el elemento carreraSelect
if (strpos($content, 'const carreraSelect = document.getElementById(\'carrera\')') !== false) {
    echo "✓ carreraSelect está siendo inicializado\n";
} else {
    echo "✗ carreraSelect NO está siendo inicializado\n";
}

// Buscar el fetch
if (strpos($content, "const response = await fetch('/api/carreras')") !== false) {
    echo "✓ fetch a /api/carreras está presente\n";
} else {
    echo "✗ fetch a /api/carreras NO está presente\n";
}

// Verificar que el select tiene el id correcto
if (strpos($content, 'id="carrera"') !== false) {
    echo "✓ select con id='carrera' existe en el HTML\n";
} else {
    echo "✗ select con id='carrera' NO existe\n";
}

?>
