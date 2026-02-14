<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require '../../conexion.php';        
require '../funciones/conciertos.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $lugar = $_GET['lugar'] ?? null;
    echo json_encode(obtenerConciertos($mysqli, $lugar));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json = json_decode(file_get_contents("php://input"), true);
    echo json_encode(['success' => insertarConcierto($mysqli, $json)]);
}