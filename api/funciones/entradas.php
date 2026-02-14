<?php
function obtenerEntradas($mysqli, $id_usuario = null) {
    $sql = "SELECT * FROM entradas";
    if ($id_usuario) {
        $sql .= " WHERE id_usuario = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $id_usuario);
    } else {
        $stmt = $mysqli->prepare($sql);
    }
    $stmt->execute();
    $res = $stmt->get_result();
    $data = $res->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $data;
}

function insertarEntrada($mysqli, $data) {
    $stmt = $mysqli->prepare("INSERT INTO entradas (id_usuario, id_concierto) VALUES (?, ?)");
    $stmt->bind_param("ii", $data['id_usuario'], $data['id_concierto']);
    $resp = $stmt->execute();
    $stmt->close();
    return $resp;
}