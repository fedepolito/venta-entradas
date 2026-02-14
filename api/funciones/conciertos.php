<?php
function obtenerConciertos($mysqli, $lugar = null) {
    $sql = "SELECT * FROM conciertos";
    if ($lugar) {
        $sql .= " WHERE lugar LIKE ?";
        $stmt = $mysqli->prepare($sql);
        $lugar = "%$lugar%";
        $stmt->bind_param("s", $lugar);
    } else {
        $stmt = $mysqli->prepare($sql);
    }
    $stmt->execute();
    $resultado = $stmt->get_result();
    $data = $resultado->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $data;
}

function insertarConcierto($mysqli, $data) {
    $stmt = $mysqli->prepare("INSERT INTO conciertos (fecha, lugar) VALUES (?, ?)");
    $stmt->bind_param("ss", $data['fecha'], $data['lugar']);
    $resp = $stmt->execute();
    $stmt->close();
    return $resp;
}