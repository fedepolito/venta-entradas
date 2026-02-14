<?php
/**
 * Endpoint para listar todos los usuarios
 * Método: GET
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Manejar preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Solo permitir método GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido. Use GET.'
    ]);
    exit();
}

// Incluir conexión y funciones
require_once __DIR__ . '/../api/conexion.php';
require_once __DIR__ . '/../api/funciones/usuarios.php';

try {
    // Obtener todos los usuarios
    $usuarios = obtenerUsuarios($mysqli);
    
    // Formatear respuesta
    $response = [
        'success' => true,
        'count' => count($usuarios),
        'data' => []
    ];
    
    // Procesar cada usuario (ocultar datos sensibles)
    foreach ($usuarios as $usuario) {
        $response['data'][] = [
            'id_usuario' => $usuario['id_usuario'],
            'email' => $usuario['email'],
            'dni' => $usuario['dni'],
            'edad' => $usuario['edad'],
            'nacimiento' => $usuario['nacimiento'],
            'telefono' => $usuario['telefono'] ?? null,
            'fecha_registro' => $usuario['fecha_registro'] ?? null,
            'activo' => (bool)($usuario['activo'] ?? true)
        ];
    }
    
    http_response_code(200);
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    error_log("Listado Usuarios Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener los usuarios',
        'error' => $e->getMessage()
    ]);
}

// Cerrar conexión
$mysqli->close();
?>