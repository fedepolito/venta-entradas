<?php
/**
 * Actualizar datos de un usuario
 */

require '../api/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: listado.php");
    exit;
}

$id = $_POST['id_usuario'] ?? null;

if (!$id) {
    $_SESSION['error'] = "ID de usuario no válido";
    header("Location: listado.php");
    exit;
}

// Preparar datos para actualizar
$data = [];

if (!empty($_POST['email'])) {
    $data['email'] = $_POST['email'];
}

if (isset($_POST['telefono'])) {
    $data['telefono'] = $_POST['telefono'];
}

if (!empty($_POST['preferencias'])) {
    $data['preferencias'] = $_POST['preferencias'];
}

if (isset($_POST['activo'])) {
    $data['activo'] = $_POST['activo'] === 'on' ? 1 : 0;
}

if (empty($data)) {
    $_SESSION['error'] = "No hay datos para actualizar";
    header("Location: editar.php?id={$id}");
    exit;
}

// Actualizar en base de datos
$stmt = $mysqli->prepare("UPDATE usuarios SET email = ?, telefono = ?, preferencias = ?, activo = ? WHERE id_usuario = ?");
$stmt->bind_param(
    "ssssi",
    $data['email'] ?? '',
    $data['telefono'] ?? null,
    $data['preferencias'] ?? null,
    $data['activo'] ?? 1,
    $id
);

if ($stmt->execute()) {
    $_SESSION['success'] = "Usuario actualizado exitosamente";
} else {
    $_SESSION['error'] = "Error al actualizar usuario: " . $stmt->error;
}

$stmt->close();
$mysqli->close();

header("Location: listado.php");
exit;
?>