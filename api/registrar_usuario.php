<?php
/**
 * Endpoint para registrar un nuevo usuario
 * Método: POST
 * Content-Type: application/json
 */

// Headers CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');

// Manejar preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Solo permitir método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido. Use POST.'
    ]);
    exit();
}

// Incluir conexión y funciones
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/funciones/usuarios.php';

// Obtener datos del request
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Validar que sea JSON válido
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'JSON inválido',
        'error' => json_last_error_msg()
    ]);
    exit();
}

// Validar campos obligatorios
$required_fields = ['email', 'dni', 'edad', 'nacimiento', 'password'];
$missing_fields = [];

foreach ($required_fields as $field) {
    if (!isset($data[$field]) || empty(trim($data[$field]))) {
        $missing_fields[] = $field;
    }
}

if (!empty($missing_fields)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Campos obligatorios faltantes',
        'missing_fields' => $missing_fields
    ]);
    exit();
}

// Validar formato de email
if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Email inválido'
    ]);
    exit();
}

// Validar DNI
if (!is_numeric($data['dni']) || strlen($data['dni']) < 7 || strlen($data['dni']) > 8) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'DNI inválido. Debe tener entre 7 y 8 dígitos'
    ]);
    exit();
}

// Validar edad
if (!is_numeric($data['edad']) || $data['edad'] < 18 || $data['edad'] > 120) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Edad inválida. Debe estar entre 18 y 120 años'
    ]);
    exit();
}

// Validar fecha de nacimiento
if (!strtotime($data['nacimiento'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Fecha de nacimiento inválida'
    ]);
    exit();
}

// Validar contraseña
if (strlen($data['password']) < 6 || strlen($data['password']) > 16) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'La contraseña debe tener entre 6 y 16 caracteres'
    ]);
    exit();
}

// Verificar si el email ya existe
$existing_user = obtenerUsuarios($mysqli, $data['email']);
if (!empty($existing_user)) {
    http_response_code(409);
    echo json_encode([
        'success' => false,
        'message' => 'El email ya está registrado'
    ]);
    exit();
}

// Intentar insertar usuario
try {
    $result = insertarUsuario($mysqli, $data);
    
    if ($result) {
        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Usuario registrado exitosamente',
            'data' => [
                'email' => $data['email'],
                'dni' => $data['dni']
            ]
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Error al registrar el usuario'
        ]);
    }
    
} catch (Exception $e) {
    error_log("Registro Usuario Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error interno del servidor',
        'error' => $e->getMessage()
    ]);
}

// Cerrar conexión
$mysqli->close();
?>
