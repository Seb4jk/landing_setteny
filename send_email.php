<?php
// Este archivo es solo para pruebas locales
// En producción, se usa Netlify Forms

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Manejar preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Recibir datos del formulario
$data = json_decode(file_get_contents("php://input"), true);

// Responder con éxito (simulación)
echo json_encode([
    "success" => true,
    "message" => "Este es un archivo de prueba para desarrollo local. En producción, se usa Netlify Forms."
]);
