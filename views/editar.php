<?php 
include '../public/base.php';
require '../api/conexion.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: listado.php");
    exit;
}

$stmt = $mysqli->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();
$stmt->close();

if (!$usuario) {
    header("Location: listado.php");
    exit;
}
?>

<div class="row justify-content-center">
  <div class="col-12 col-md-8 col-lg-6">
    <div class="card shadow-lg border-0">
      <div class="card-header bg-gradient-primary text-white text-center py-4">
        <h2 class="mb-0">
          <i class="fas fa-edit me-2"></i>Editar Usuario
        </h2>
        <p class="mb-0 mt-2">ID: <?= $usuario['id_usuario'] ?></p>
      </div>
      
      <div class="card-body p-4">
        <form method="POST" action="actualizar_usuario.php" class="needs-validation" novalidate>
          <input type="hidden" name="id_usuario" value="<?= $usuario['id_usuario'] ?>">
          
          <div class="row g-3">
            <div class="col-12">
              <label for="email" class="form-label">
                <i class="fas fa-envelope me-2"></i>Correo electrónico *
              </label>
              <input type="email" name="email" id="email" class="form-control form-control-lg" 
                     value="<?= htmlspecialchars($usuario['email']) ?>" required>
              <div class="invalid-feedback">Por favor ingresa un email válido</div>
            </div>
            
            <div class="col-12">
              <label for="telefono" class="form-label">
                <i class="fas fa-phone me-2"></i>Teléfono
              </label>
              <input type="tel" name="telefono" id="telefono" class="form-control form-control-lg" 
                     value="<?= htmlspecialchars($usuario['telefono'] ?? '') ?>">
            </div>
            
            <div class="col-12">
              <label for="preferencias" class="form-label">
                <i class="fas fa-cog me-2"></i>Preferencias (JSON)
              </label>
              <textarea name="preferencias" id="preferencias" class="form-control form-control-lg" 
                        rows="3"><?= htmlspecialchars($usuario['preferencias'] ?? '') ?></textarea>
              <small class="text-muted">Formato JSON válido (opcional)</small>
            </div>
            
            <div class="col-12">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="activo" name="activo" 
                       <?= $usuario['activo'] ? 'checked' : '' ?>>
                <label class="form-check-label" for="activo">
                  <i class="fas fa-toggle-on me-1"></i>Usuario activo
                </label>
              </div>
            </div>
          </div>
          
          <div class="d-grid gap-2 mt-4">
            <button type="submit" class="btn btn-admin btn-lg">
              <i class="fas fa-save me-2"></i>Guardar Cambios
            </button>
            <a href="listado.php" class="btn btn-outline-secondary text-center">
              <i class="fas fa-arrow-left me-2"></i>Volver al Listado
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include '../public/footer.php'; ?>

<script>
(function () {
  'use strict'
  var forms = document.querySelectorAll('.needs-validation')
  Array.prototype.slice.call(forms)
    .forEach(function (form) {
      form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
          event.preventDefault()
          event.stopPropagation()
        }
        form.classList.add('was-validated')
      }, false)
    })
})()
</script>