<?php include 'base.php'; ?>
<?php require 'conexion.php'; ?>
<?php require 'funciones/usuarios.php'; ?>

<div class="container my-5">
  <h2 class="mb-4">Listado de Usuarios</h2>

  <table class="table table-bordered table-hover">
    <thead class="table-dark text-center">
      <tr>
        <th>Email</th>
        <th>DNI</th>
        <th>Teléfono</th>
        <th>Preferencias</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach (obtenerUsuarios($mysqli) as $usuario): ?>
        <tr>
          <td><?= $usuario['email'] ?></td>
          <td><?= $usuario['dni'] ?></td>
          <td><?= $usuario['telefono'] ?? '-' ?></td>
          <td><?= $usuario['preferencias'] ?? '-' ?></td>
          <td class="text-center">
            <a href="editar.php?id=<?= $usuario['id_usuario'] ?>" class="btn btn-success text-white rounded-pill me-2">
              <i class="bi bi-pencil-square"></i> Editar
            </a>
            <a href="eliminar_usuario.php?id=<?= $usuario['id_usuario'] ?>" class="btn btn-danger text-white rounded-pill" onclick="return confirm('¿Estás seguro de eliminar este usuario?')">
              <i class="bi bi-trash"></i> Eliminar
            </a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php include 'footer.php'; ?>
