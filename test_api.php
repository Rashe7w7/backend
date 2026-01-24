<?php

// Test script to verify API endpoints

$ch = curl_init('http://127.0.0.1:8000/api/carreras');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Code: " . $httpCode . "\n";
if ($error) {
    echo "Error: " . $error . "\n";
} else {
    echo "Response: " . $response . "\n";
}
?>
