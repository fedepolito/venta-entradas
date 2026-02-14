<?php
/**
 * API REST para gestión de conciertos
 * Métodos: GET, POST
 */

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../../api/conexion.php';
require_once __DIR__ . '/../funciones/conciertos.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            handleGetConciertos($mysqli);
            break;
            
        case 'POST':
            handlePostConcierto($mysqli);
            break;
            
        default:
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'message' => 'Método no permitido'
            ]);
    }
    
} catch (Exception $e) {
    error_log("Conciertos API Error: " . $e->getMessage());
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
function handleGetConciertos($mysqli) {
    $lugar = $_GET['lugar'] ?? null;
    $fecha = $_GET['fecha'] ?? null;
    $id = $_GET['id'] ?? null;
    
    if ($id) {
        // Obtener concierto por ID con estadísticas
        $concierto = obtenerEstadisticasConcierto($mysqli, $id);
        
        if ($concierto) {
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'data' => $concierto
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'Concierto no encontrado'
            ]);
        }
        
    } else {
        // Obtener todos los conciertos o filtrar
        $conciertos = obtenerConciertos($mysqli, $lugar, $fecha);
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'count' => count($conciertos),
            'data' => $conciertos
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}

/**
 * Manejar POST request
 */
function handlePostConcierto($mysqli) {
    $json = file_get_contents("php://input");
    $data = json_decode($json, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'JSON inválido']);
        return;
    }
    
    // Validar campos requeridos
    if (!isset($data['fecha'], $data['lugar'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Campos requeridos: fecha, lugar'
        ]);
        return;
    }
    
    // Insertar concierto
    $id = insertarConcierto($mysqli, $data);
    
    if ($id) {
        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Concierto creado exitosamente',
            'data' => [
                'id_concierto' => $id,
                'fecha' => $data['fecha'],
                'lugar' => $data['lugar']
            ]
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Error al crear concierto'
        ]);
    }
}
?>