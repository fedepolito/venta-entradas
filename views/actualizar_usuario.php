<?php
require 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id_usuario'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'] ?? null;
    $preferencias = $_POST['preferencias'] ?? null;

    $stmt = $mysqli->prepare("UPDATE usuarios SET email = ?, telefono = ?, preferencias = ? WHERE id_usuario = ?");
    $stmt->bind_param("sssi", $email, $telefono, $preferencias, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: listado.php");
    exit;
}
