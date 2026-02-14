<?php
/**
 * Funciones CRUD para la tabla usuarios
 * Incluye validaciones y manejo de errores
 */

/**
 * Obtener usuarios de la base de datos
 * @param mysqli $mysqli Conexión a la base de datos
 * @param string $email Email para filtrar (opcional)
 * @return array Array de usuarios
 */
function obtenerUsuarios($mysqli, $email = null) {
    $sql = "SELECT id_usuario, email, dni, edad, nacimiento, telefono, fecha_registro, activo, preferencias 
            FROM usuarios";
    
    if ($email) {
        // Validar email antes de usar en query
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [];
        }
        
        $sql .= " WHERE email LIKE ?";
        $stmt = $mysqli->prepare($sql);
        $email_param = "%{$email}%";
        $stmt->bind_param("s", $email_param);
    } else {
        $stmt = $mysqli->prepare($sql);
    }
    
    if (!$stmt) {
        error_log("Error preparing statement: " . $mysqli->error);
        return [];
    }
    
    $stmt->execute();
    $resultado = $stmt->get_result();
    $data = $resultado->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
    return $data;
}

/**
 * Insertar un nuevo usuario en la base de datos
 * @param mysqli $mysqli Conexión a la base de datos
 * @param array $data Datos del usuario
 * @return bool True si se insertó correctamente, false en caso contrario
 */
function insertarUsuario($mysqli, $data) {
    // Validar datos antes de insertar
    if (!isset($data['email'], $data['dni'], $data['edad'], $data['nacimiento'], $data['password'])) {
        error_log("Datos incompletos para insertar usuario");
        return false;
    }
    
    // Encriptar contraseña
    $passSegura = password_hash($data['password'], PASSWORD_DEFAULT);
    
    // Preparar statement
    $stmt = $mysqli->prepare(
        "INSERT INTO usuarios (email, dni, edad, nacimiento, password, telefono, preferencias) 
         VALUES (?, ?, ?, ?, ?, ?, ?)"
    );
    
    if (!$stmt) {
        error_log("Error preparing insert statement: " . $mysqli->error);
        return false;
    }
    
    // Obtener datos opcionales
    $telefono = $data['telefono'] ?? null;
    $preferencias = isset($data['preferencias']) ? json_encode($data['preferencias']) : null;
    
    // Bind parameters
    $stmt->bind_param(
        "siissis",
        $data['email'],
        $data['dni'],
        $data['edad'],
        $data['nacimiento'],
        $passSegura,
        $telefono,
        $preferencias
    );
    
    // Ejecutar
    $resp = $stmt->execute();
    
    if (!$resp) {
        error_log("Error executing insert: " . $stmt->error);
    }
    
    $stmt->close();
    return $resp;
}

/**
 * Actualizar un usuario existente
 * @param mysqli $mysqli Conexión a la base de datos
 * @param int $id ID del usuario
 * @param array $data Datos a actualizar
 * @return bool True si se actualizó correctamente
 */
function actualizarUsuario($mysqli, $id, $data) {
    $updates = [];
    $types = '';
    $values = [];
    
    // Construir query dinámicamente
    if (isset($data['email'])) {
        $updates[] = "email = ?";
        $types .= 's';
        $values[] = $data['email'];
    }
    
    if (isset($data['telefono'])) {
        $updates[] = "telefono = ?";
        $types .= 's';
        $values[] = $data['telefono'];
    }
    
    if (isset($data['preferencias'])) {
        $updates[] = "preferencias = ?";
        $types .= 's';
        $values[] = json_encode($data['preferencias']);
    }
    
    if (isset($data['activo'])) {
        $updates[] = "activo = ?";
        $types .= 'i';
        $values[] = $data['activo'] ? 1 : 0;
    }
    
    if (empty($updates)) {
        return false;
    }
    
    $sql = "UPDATE usuarios SET " . implode(', ', $updates) . " WHERE id_usuario = ?";
    $stmt = $mysqli->prepare($sql);
    
    if (!$stmt) {
        return false;
    }
    
    // Agregar ID al final
    $types .= 'i';
    $values[] = $id;
    
    // Bind dinámico
    $stmt->bind_param($types, ...$values);
    $result = $stmt->execute();
    $stmt->close();
    
    return $result;
}

/**
 * Eliminar un usuario
 * @param mysqli $mysqli Conexión a la base de datos
 * @param int $id ID del usuario
 * @return bool True si se eliminó correctamente
 */
function eliminarUsuario($mysqli, $id) {
    $stmt = $mysqli->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
    
    if (!$stmt) {
        return false;
    }
    
    $stmt->bind_param("i", $id);
    $result = $stmt->execute();
    $stmt->close();
    
    return $result;
}

/**
 * Verificar credenciales de usuario
 * @param mysqli $mysqli Conexión a la base de datos
 * @param string $email Email del usuario
 * @param string $password Contraseña proporcionada
 * @return array|null Datos del usuario si las credenciales son válidas, null en caso contrario
 */
function verificarUsuario($mysqli, $email, $password) {
    $stmt = $mysqli->prepare("SELECT * FROM usuarios WHERE email = ? AND activo = 1");
    
    if (!$stmt) {
        return null;
    }
    
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $usuario = $resultado->fetch_assoc();
    $stmt->close();
    
    // Verificar contraseña
    if ($usuario && password_verify($password, $usuario['password'])) {
        // No devolver la contraseña
        unset($usuario['password']);
        return $usuario;
    }
    
    return null;
}
?>
