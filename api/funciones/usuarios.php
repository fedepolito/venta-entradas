<?php
function obtenerUsuarios($mysqli, $email = null) {
    $sql = "SELECT * FROM usuarios";
    if ($email) {
        $sql .= " WHERE email LIKE ?";
        $stmt = $mysqli->prepare($sql);
        $email = "%$email%";
        $stmt->bind_param("s", $email);
    } else {
        $stmt = $mysqli->prepare($sql);
    }
    $stmt->execute();
    $resultado = $stmt->get_result();
    $data = $resultado->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $data;
}

function insertarUsuario($mysqli, $data) {
    $stmt = $mysqli->prepare("INSERT INTO usuarios (email, dni, edad, nacimiento, password) VALUES (?, ?, ?, ?, ?)");
    $passSegura = password_hash($data['password'], PASSWORD_DEFAULT);
    $stmt->bind_param("ssiss", $data['email'], $data['dni'], $data['edad'], $data['nacimiento'], $passSegura);
    $resp = $stmt->execute();
    $stmt->close();
    return $resp;
}
