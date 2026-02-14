<?php
require 'conexion.php';

$id = $_GET['id'] ?? null;
if ($id) {
    $stmt = $mysqli->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

header("Location: listado.php");
exit;
