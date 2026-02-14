<?php
/**
 * Funciones CRUD para la tabla conciertos
 */

/**
 * Obtener conciertos de la base de datos
 * @param mysqli $mysqli Conexión a la base de datos
 * @param string $lugar Lugar para filtrar (opcional)
 * @param string $fecha Fecha para filtrar (opcional)
 * @return array Array de conciertos
 */
function obtenerConciertos($mysqli, $lugar = null, $fecha = null) {
    $sql = "SELECT c.*, COUNT(e.id_entrada) as entradas_vendidas 
            FROM conciertos c 
            LEFT JOIN entradas e ON c.id_concierto = e.id_concierto";
    $where = [];
    $params = [];
    $types = '';
    
    if ($lugar) {
        $where[] = "c.lugar LIKE ?";
        $params[] = "%{$lugar}%";
        $types .= 's';
    }
    
    if ($fecha) {
        $where[] = "c.fecha = ?";
        $params[] = $fecha;
        $types .= 's';
    }
    
    if (!empty($where)) {
        $sql .= " WHERE " . implode(' AND ', $where);
    }
    
    $sql .= " GROUP BY c.id_concierto ORDER BY c.fecha ASC";
    
    $stmt = $mysqli->prepare($sql);
    
    if (!$stmt) {
        error_log("Error preparing conciertos statement: " . $mysqli->error);
        return [];
    }
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $resultado = $stmt->get_result();
    $data = $resultado->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
    return $data;
}

/**
 * Insertar un nuevo concierto
 * @param mysqli $mysqli Conexión a la base de datos
 * @param array $data Datos del concierto
 * @return bool|int ID del concierto insertado o false en caso de error
 */
function insertarConcierto($mysqli, $data) {
    // Validar datos requeridos
    if (!isset($data['fecha'], $data['lugar'])) {
        error_log("Datos incompletos para insertar concierto");
        return false;
    }
    
    // Validar fecha
    if (!strtotime($data['fecha'])) {
        error_log("Fecha inválida para concierto");
        return false;
    }
    
    $stmt = $mysqli->prepare(
        "INSERT INTO conciertos (fecha, lugar, artista, capacidad, precio) 
         VALUES (?, ?, ?, ?, ?)"
    );
    
    if (!$stmt) {
        error_log("Error preparing concierto insert: " . $mysqli->error);
        return false;
    }
    
    // Obtener datos opcionales
    $artista = $data['artista'] ?? 'Artista no especificado';
    $capacidad = $data['capacidad'] ?? 1000;
    $precio = $data['precio'] ?? 0.0;
    
    $stmt->bind_param("sssdi", $data['fecha'], $data['lugar'], $artista, $precio, $capacidad);
    $resp = $stmt->execute();
    
    if ($resp) {
        $id = $stmt->insert_id;
        $stmt->close();
        return $id;
    } else {
        error_log("Error executing concierto insert: " . $stmt->error);
        $stmt->close();
        return false;
    }
}

/**
 * Actualizar un concierto existente
 * @param mysqli $mysqli Conexión a la base de datos
 * @param int $id ID del concierto
 * @param array $data Datos a actualizar
 * @return bool True si se actualizó correctamente
 */
function actualizarConcierto($mysqli, $id, $data) {
    $updates = [];
    $types = '';
    $values = [];
    
    if (isset($data['fecha'])) {
        $updates[] = "fecha = ?";
        $types .= 's';
        $values[] = $data['fecha'];
    }
    
    if (isset($data['lugar'])) {
        $updates[] = "lugar = ?";
        $types .= 's';
        $values[] = $data['lugar'];
    }
    
    if (isset($data['artista'])) {
        $updates[] = "artista = ?";
        $types .= 's';
        $values[] = $data['artista'];
    }
    
    if (isset($data['capacidad'])) {
        $updates[] = "capacidad = ?";
        $types .= 'i';
        $values[] = $data['capacidad'];
    }
    
    if (isset($data['precio'])) {
        $updates[] = "precio = ?";
        $types .= 'd';
        $values[] = $data['precio'];
    }
    
    if (empty($updates)) {
        return false;
    }
    
    $sql = "UPDATE conciertos SET " . implode(', ', $updates) . " WHERE id_concierto = ?";
    $stmt = $mysqli->prepare($sql);
    
    if (!$stmt) {
        return false;
    }
    
    $types .= 'i';
    $values[] = $id;
    
    $stmt->bind_param($types, ...$values);
    $result = $stmt->execute();
    $stmt->close();
    
    return $result;
}

/**
 * Eliminar un concierto
 * @param mysqli $mysqli Conexión a la base de datos
 * @param int $id ID del concierto
 * @return bool True si se eliminó correctamente
 */
function eliminarConcierto($mysqli, $id) {
    $stmt = $mysqli->prepare("DELETE FROM conciertos WHERE id_concierto = ?");
    
    if (!$stmt) {
        return false;
    }
    
    $stmt->bind_param("i", $id);
    $result = $stmt->execute();
    $stmt->close();
    
    return $result;
}

/**
 * Obtener estadísticas de un concierto
 * @param mysqli $mysqli Conexión a la base de datos
 * @param int $id ID del concierto
 * @return array|null Estadísticas del concierto
 */
function obtenerEstadisticasConcierto($mysqli, $id) {
    $stmt = $mysqli->prepare(
        "SELECT c.*, COUNT(e.id_entrada) as entradas_vendidas,
         (c.capacidad - COUNT(e.id_entrada)) as entradas_disponibles
         FROM conciertos c
         LEFT JOIN entradas e ON c.id_concierto = e.id_concierto
         WHERE c.id_concierto = ?
         GROUP BY c.id_concierto"
    );
    
    if (!$stmt) {
        return null;
    }
    
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $data = $resultado->fetch_assoc();
    $stmt->close();
    
    return $data;
}
?>