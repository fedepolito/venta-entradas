<?php include 'base.php'; ?>
<?php require '../conexion.php'; ?>
<?php require '../funciones/usuarios.php'; ?>

<?php
$id = $_GET['id'] ?? null;
if (!$id) {
  echo "<div class='container my-5'><p>ID no válido.</p></div>";
  include 'footer.php';
  exit;
}

$stmt = $mysqli->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();
$stmt->close();
?>

<div class="container my-5">
  <h2 class="mb-4">Editar Usuario</h2>

  <form method="POST" action="actualizar_usuario.php" class="p-4 bg-light rounded">
    <input type="hidden" name="id_usuario" value="<?= $usuario['id_usuario'] ?>">

    <label for="email" class="form-label">Correo electrónico *</label>
    <input type="email" name="email" id="email" class="form-control mb-3" required value="<?= $usuario['email'] ?>">

    <label for="telefono" class="form-label">Teléfono</label>
    <input type="tel" name="telefono" id="telefono" class="form-control mb-3" value="<?= $usuario['telefono'] ?? '' ?>">

    <label for="preferencias" class="form-label">Preferencias (JSON)</label>
    <textarea name="preferencias" id="preferencias" class="form-control mb-3" rows="3"><?= $usuario['preferencias'] ?? '' ?></textarea>

    <button type="submit" class="btn btn-success text-white rounded-pill">
      <i class="bi bi-save"></i> Guardar cambios
    </button>
  </form>
</div>

<?php include 'footer.php'; ?>
