<?php 
include '../public/base.php';
require '../api/conexion.php';
require '../api/funciones/usuarios.php';
?>

<div class="card shadow-lg border-0">
  <div class="card-header bg-gradient-primary text-white py-4">
    <div class="d-flex justify-content-between align-items-center">
      <h2 class="mb-0">
        <i class="fas fa-list me-2"></i>Listado de Usuarios
      </h2>
      <span class="badge bg-light text-dark fs-6">
        <i class="fas fa-users me-1"></i>
        <?php 
        $total = obtenerUsuarios($mysqli);
        echo count($total);
        ?> usuarios
      </span>
    </div>
  </div>
  
  <div class="card-body p-4">
    <div class="table-responsive">
      <table class="table table-hover table-striped">
        <thead class="table-dark">
          <tr>
            <th><i class="fas fa-id me-1"></i> ID</th>
            <th><i class="fas fa-envelope me-1"></i> Email</th>
            <th><i class="fas fa-id-card me-1"></i> DNI</th>
            <th><i class="fas fa-phone me-1"></i> Teléfono</th>
            <th><i class="fas fa-calendar me-1"></i> Registro</th>
            <th><i class="fas fa-toggle-on me-1"></i> Estado</th>
            <th><i class="fas fa-cogs me-1"></i> Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          $usuarios = obtenerUsuarios($mysqli);
          foreach ($usuarios as $usuario): 
          ?>
            <tr>
              <td class="align-middle"><?= $usuario['id_usuario'] ?></td>
              <td class="align-middle">
                <a href="mailto:<?= $usuario['email'] ?>" class="text-decoration-none">
                  <?= $usuario['email'] ?>
                </a>
              </td>
              <td class="align-middle"><?= $usuario['dni'] ?></td>
              <td class="align-middle">
                <?= $usuario['telefono'] ?? '<span class="text-muted">-</span>' ?>
              </td>
              <td class="align-middle">
                <small><?= date('d/m/Y', strtotime($usuario['fecha_registro'])) ?></small>
              </td>
              <td class="align-middle">
                <?php if ($usuario['activo']): ?>
                  <span class="badge bg-success">
                    <i class="fas fa-check me-1"></i>Activo
                  </span>
                <?php else: ?>
                  <span class="badge bg-danger">
                    <i class="fas fa-times me-1"></i>Inactivo
                  </span>
                <?php endif; ?>
              </td>
              <td class="align-middle">
                <div class="d-flex gap-2">
                  <a href="editar.php?id=<?= $usuario['id_usuario'] ?>" 
                     class="btn btn-sm btn-warning text-white" 
                     title="Editar">
                    <i class="fas fa-edit"></i>
                  </a>
                  <a href="eliminar_usuario.php?id=<?= $usuario['id_usuario'] ?>" 
                     class="btn btn-sm btn-danger" 
                     title="Eliminar"
                     onclick="return confirm('¿Estás seguro de eliminar este usuario? Esta acción no se puede deshacer.')">
                    <i class="fas fa-trash"></i>
                  </a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include '../public/footer.php'; ?>