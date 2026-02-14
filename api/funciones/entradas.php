<?php
/**
 * Funciones CRUD para la tabla entradas
 */

/**
 * Obtener entradas de la base de datos
 * @param mysqli $mysqli Conexión a la base de datos
 * @param int $id_usuario ID del usuario para filtrar (opcional)
 * @param int $id_concierto ID del concierto para filtrar (opcional)
 * @return array Array de entradas
 */
function obtenerEntradas($mysqli, $id_usuario = null, $id_concierto = null) {
    $sql = "SELECT e.*, 
                   u.email as usuario_email, 
                   u.dni as usuario_dni,
                   c.fecha as concierto_fecha,
                   c.lugar as concierto_lugar,
                   c.artista as concierto_artista
            FROM entradas e
            INNER JOIN usuarios u ON e.id_usuario = u.id_usuario
            INNER JOIN conciertos c ON e.id_concierto = c.id_concierto";
    $where = [];
    $params = [];
    $types = '';
    
    if ($id_usuario) {
        $where[] = "e.id_usuario = ?";
        $params[] = $id_usuario;
        $types .= 'i';
    }
    
    if ($id_concierto) {
        $where[] = "e.id_concierto = ?";
        $params[] = $id_concierto;
        $types .= 'i';
    }
    
    if (!empty($where)) {
        $sql .= " WHERE " . implode(' AND ', $where);
    }
    
    $sql .= " ORDER BY e.id_entrada DESC";
    
    $stmt = $mysqli->prepare($sql);
    
    if (!$stmt) {
        error_log("Error preparing entradas statement: " . $mysqli->error);
        return [];
    }
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $res = $stmt->get_result();
    $data = $res->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
    return $data;
}

/**
 * Insertar una nueva entrada
 * @param mysqli $mysqli Conexión a la base de datos
 * @param array $data Datos de la entrada
 * @return bool|int ID de la entrada insertada o false en caso de error
 */
function insertarEntrada($mysqli, $data) {
    // Validar datos requeridos
    if (!isset($data['id_usuario'], $data['id_concierto'])) {
        error_log("Datos incompletos para insertar entrada");
        return false;
    }
    
    // Verificar que el usuario exista
    $user_check = $mysqli->prepare("SELECT id_usuario FROM usuarios WHERE id_usuario = ?");
    $user_check->bind_param("i", $data['id_usuario']);
    $user_check->execute();
    $user_result = $user_check->get_result();
    
    if ($user_result->num_rows === 0) {
        error_log("Usuario no encontrado para entrada");
        $user_check->close();
        return false;
    }
    $user_check->close();
    
    // Verificar que el concierto exista
    $concert_check = $mysqli->prepare("SELECT id_concierto FROM conciertos WHERE id_concierto = ?");
    $concert_check->bind_param("i", $data['id_concierto']);
    $concert_check->execute();
    $concert_result = $concert_check->get_result();
    
    if ($concert_result->num_rows === 0) {
        error_log("Concierto no encontrado para entrada");
        $concert_check->close();
        return false;
    }
    $concert_check->close();
    
    // Verificar si el usuario ya tiene una entrada para este concierto
    $duplicate_check = $mysqli->prepare(
        "SELECT id_entrada FROM entradas WHERE id_usuario = ? AND id_concierto = ?"
    );
    $duplicate_check->bind_param("ii", $data['id_usuario'], $data['id_concierto']);
    $duplicate_check->execute();
    $duplicate_result = $duplicate_check->get_result();
    
    if ($duplicate_result->num_rows > 0) {
        error_log("El usuario ya tiene una entrada para este concierto");
        $duplicate_check->close();
        return false;
    }
    $duplicate_check->close();
    
    // Insertar entrada
    $stmt = $mysqli->prepare("INSERT INTO entradas (id_usuario, id_concierto) VALUES (?, ?)");
    
    if (!$stmt) {
        error_log("Error preparing entrada insert: " . $mysqli->error);
        return false;
    }
    
    $stmt->bind_param("ii", $data['id_usuario'], $data['id_concierto']);
    $resp = $stmt->execute();
    
    if ($resp) {
        $id = $stmt->insert_id;
        $stmt->close();
        return $id;
    } else {
        error_log("Error executing entrada insert: " . $stmt->error);
        $stmt->close();
        return false;
    }
}

/**
 * Eliminar una entrada
 * @param mysqli $mysqli Conexión a la base de datos
 * @param int $id ID de la entrada
 * @return bool True si se eliminó correctamente
 */
function eliminarEntrada($mysqli, $id) {
    $stmt = $mysqli->prepare("DELETE FROM entradas WHERE id_entrada = ?");
    
    if (!$stmt) {
        return false;
    }
    
    $stmt->bind_param("i", $id);
    $result = $stmt->execute();
    $stmt->close();
    
    return $result;
}

/**
 * Obtener entradas por usuario con detalles
 * @param mysqli $mysqli Conexión a la base de datos
 * @param int $id_usuario ID del usuario
 * @return array Array de entradas con detalles
 */
function obtenerEntradasPorUsuario($mysqli, $id_usuario) {
    $stmt = $mysqli->prepare(
        "SELECT e.id_entrada, e.fecha_compra,
                c.fecha as concierto_fecha, c.lugar, c.artista, c.precio,
                (SELECT COUNT(*) FROM entradas WHERE id_concierto = c.id_concierto) as total_vendidas
         FROM entradas e
         INNER JOIN conciertos c ON e.id_concierto = c.id_concierto
         WHERE e.id_usuario = ?
         ORDER BY c.fecha DESC"
    );
    
    if (!$stmt) {
        return [];
    }
    
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $data = $resultado->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
    return $data;
}

/**
 * Obtener total de entradas vendidas
 * @param mysqli $mysqli Conexión a la base de datos
 * @return int Total de entradas vendidas
 */
function getTotalEntradasVendidas($mysqli) {
    $stmt = $mysqli->prepare("SELECT COUNT(*) as total FROM entradas");
    
    if (!$stmt) {
        return 0;
    }
    
    $stmt->execute();
    $resultado = $stmt->get_result();
    $row = $resultado->fetch_assoc();
    $stmt->close();
    
    return (int)$row['total'];
}
?>