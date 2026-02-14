<?php
/**
 * Eliminar un usuario
 */

require '../api/conexion.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: listado.php");
    exit;
}

// Verificar que el usuario exista
$check_stmt = $mysqli->prepare("SELECT id_usuario FROM usuarios WHERE id_usuario = ?");
$check_stmt->bind_param("i", $id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows === 0) {
    $_SESSION['error'] = "Usuario no encontrado";
    $check_stmt->close();
    header("Location: listado.php");
    exit;
}
$check_stmt->close();

// Eliminar usuario
$stmt = $mysqli->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $_SESSION['success'] = "Usuario eliminado exitosamente";
} else {
    $_SESSION['error'] = "Error al eliminar usuario: " . $stmt->error;
}

$stmt->close();
$mysqli->close();

header("Location: listado.php");
exit;
?>