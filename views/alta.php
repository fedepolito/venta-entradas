<?php include 'base.php'; ?>

<div class="container my-5">
  <h2 class="mb-4">Alta de Usuario</h2>

  <form method="POST" action="registrar_usuario.php" class="p-4 bg-light rounded">
    <label for="email" class="form-label">Correo electrónico *</label>
    <input type="email" name="email" id="email" class="form-control mb-3" required>

    <label for="dni" class="form-label">DNI *</label>
    <input type="number" name="dni" id="dni" class="form-control mb-3" required>

    <label for="telefono" class="form-label">Teléfono</label>
    <input type="tel" name="telefono" id="telefono" class="form-control mb-3">

    <label for="preferencias" class="form-label">Preferencias (JSON)</label>
    <textarea name="preferencias" id="preferencias" class="form-control mb-3" rows="3" placeholder='{"newsletter":true}'></textarea>

    <button type="submit" class="btn btn-success text-white rounded-pill">
      <i class="bi bi-check-circle"></i> Registrar
    </button>
  </form>
</div>

<?php include 'footer.php'; ?>
