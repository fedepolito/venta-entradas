<?php
/**
 * API REST para gestión de entradas
 * Métodos: GET, POST
 */

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../../api/conexion.php';
require_once __DIR__ . '/../funciones/entradas.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            handleGetEntradas($mysqli);
            break;
            
        case 'POST':
            handlePostEntrada($mysqli);
            break;
            
        default:
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'message' => 'Método no permitido'
            ]);
    }
    
} catch (Exception $e) {
    error_log("Entradas API Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error interno del servidor',
        'error' => $e->getMessage()
    ]);
}

$mysqli->close();

/**
 * Manejar GET request
 */
function handleGetEntradas($mysqli) {
    $id_usuario = $_GET['id_usuario'] ?? null;
    $id_concierto = $_GET['id_concierto'] ?? null;
    
    $entradas = obtenerEntradas($mysqli, $id_usuario, $id_concierto);
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'count' => count($entradas),
        'data' => $entradas
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

/**
 * Manejar POST request
 */
function handlePostEntrada($mysqli) {
    $json = file_get_contents("php://input");
    $data = json_decode($json, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'JSON inválido']);
        return;
    }
    
    // Validar campos requeridos
    if (!isset($data['id_usuario'], $data['id_concierto'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Campos requeridos: id_usuario, id_concierto'
        ]);
        return;
    }
    
    // Insertar entrada
    $id = insertarEntrada($mysqli, $data);
    
    if ($id) {
        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Entrada creada exitosamente',
            'data' => [
                'id_entrada' => $id,
                'id_usuario' => $data['id_usuario'],
                'id_concierto' => $data['id_concierto']
            ]
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'No se pudo crear la entrada. Verifica que el usuario y concierto existan y que no haya duplicados.'
        ]);
    }
}
?>