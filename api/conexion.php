<?php
/**
 * Conexión a la base de datos con manejo de errores
 */

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'entradas_online');

// Configuración de errores (desactivar en producción)
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');

/**
 * Establecer conexión con la base de datos
 * @return mysqli|null
 */
function getDatabaseConnection() {
    try {
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($mysqli->connect_error) {
            throw new Exception("Error de conexión: " . $mysqli->connect_error);
        }
        
        // Establecer charset UTF-8
        $mysqli->set_charset("utf8mb4");
        
        return $mysqli;
        
    } catch (Exception $e) {
        error_log("Database Connection Error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Error al conectar con la base de datos',
            'error' => $e->getMessage()
        ]);
        return null;
    }
}

// Establecer conexión
$mysqli = getDatabaseConnection();

// Si hay error en la conexión, terminar script
if (!$mysqli) {
    exit;
}