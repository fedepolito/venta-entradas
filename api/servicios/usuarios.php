<?php
/**
 * API REST para gestión de usuarios
 * Métodos: GET, POST
 */

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json; charset=utf-8');

// Manejar preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../../api/conexion.php';
require_once __DIR__ . '/../funciones/usuarios.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            handleGetUsuarios($mysqli);
            break;
            
        case 'POST':
            handlePostUsuario($mysqli);
            break;
            
        case 'PUT':
            handlePutUsuario($mysqli);
            break;
            
        case 'DELETE':
            handleDeleteUsuario($mysqli);
            break;
            
        default:
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'message' => 'Método no permitido'
            ]);
    }
    
} catch (Exception $e) {
    error_log("Usuarios API Error: " . $e->getMessage());
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
function handleGetUsuarios($mysqli) {
    $email = $_GET['email'] ?? null;
    $id = $_GET['id'] ?? null;
    
    if ($id) {
        // Obtener usuario por ID
        $stmt = $mysqli->prepare("SELECT id_usuario, email, dni, edad, nacimiento, telefono, fecha_registro, activo FROM usuarios WHERE id_usuario = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $usuario = $resultado->fetch_assoc();
        $stmt->close();
        
        if ($usuario) {
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'data' => $usuario
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ]);
        }
        
    } else {
        // Obtener todos los usuarios o filtrar por email
        $usuarios = obtenerUsuarios($mysqli, $email);
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'count' => count($usuarios),
            'data' => $usuarios
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}

/**
 * Manejar POST request
 */
function handlePostUsuario($mysqli) {
    $json = file_get_contents("php://input");
    $data = json_decode($json, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'JSON inválido'
        ]);
        return;
    }
    
    // Validar campos obligatorios
    $required = ['email', 'dni', 'edad', 'nacimiento', 'password'];
    foreach ($required as $field) {
        if (!isset($data[$field])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => "Campo requerido faltante: {$field}"
            ]);
            return;
        }
    }
    
    // Validar email
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Email inválido'
        ]);
        return;
    }
    
    // Verificar si email ya existe
    $existing = obtenerUsuarios($mysqli, $data['email']);
    if (!empty($existing)) {
        http_response_code(409);
        echo json_encode([
            'success' => false,
            'message' => 'El email ya está registrado'
        ]);
        return;
    }
    
    // Insertar usuario
    $result = insertarUsuario($mysqli, $data);
    
    if ($result) {
        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Usuario creado exitosamente',
            'data' => [
                'email' => $data['email'],
                'dni' => $data['dni']
            ]
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Error al crear usuario'
        ]);
    }
}

/**
 * Manejar PUT request
 */
function handlePutUsuario($mysqli) {
    $json = file_get_contents("php://input");
    $data = json_decode($json, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'JSON inválido']);
        return;
    }
    
    $id = $_GET['id'] ?? null;
    
    if (!$id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID de usuario requerido']);
        return;
    }
    
    $result = actualizarUsuario($mysqli, $id, $data);
    
    if ($result) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Usuario actualizado exitosamente'
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Error al actualizar usuario'
        ]);
    }
}

/**
 * Manejar DELETE request
 */
function handleDeleteUsuario($mysqli) {
    $id = $_GET['id'] ?? null;
    
    if (!$id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID de usuario requerido']);
        return;
    }
    
    $result = eliminarUsuario($mysqli, $id);
    
    if ($result) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Usuario eliminado exitosamente'
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Error al eliminar usuario'
        ]);
    }
}
?>
