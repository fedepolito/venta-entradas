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
require '../funciones/entradas.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = $_GET['id_usuario'] ?? null;
    echo json_encode(obtenerEntradas($mysqli, $id));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json = json_decode(file_get_contents("php://input"), true);
    echo json_encode(['success' => insertarEntrada($mysqli, $json)]);
}