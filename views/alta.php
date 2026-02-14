<?php include '../public/base.php'; ?>

<div class="row justify-content-center">
  <div class="col-12 col-md-8 col-lg-6">
    <div class="card shadow-lg border-0">
      <div class="card-header bg-gradient-primary text-white text-center py-4">
        <h2 class="mb-0">
          <i class="fas fa-user-plus me-2"></i>Alta de Usuario
        </h2>
        <p class="mb-0 mt-2">Completa los datos para registrar un nuevo usuario</p>
      </div>
      
      <div class="card-body p-4">
        <form method="POST" action="registrar_usuario.php" class="needs-validation" novalidate>
          <div class="row g-3">
            <div class="col-12">
              <label for="email" class="form-label">
                <i class="fas fa-envelope me-2"></i>Correo electrónico *
              </label>
              <input type="email" name="email" id="email" class="form-control form-control-lg" 
                     placeholder="ejemplo@gmail.com" required>
              <div class="invalid-feedback">Por favor ingresa un email válido</div>
            </div>
            
            <div class="col-12">
              <label for="dni" class="form-label">
                <i class="fas fa-id-card me-2"></i>DNI *
              </label>
              <input type="number" name="dni" id="dni" class="form-control form-control-lg" 
                     placeholder="12345678" required min="1000000" max="99999999">
              <div class="invalid-feedback">Ingresa un DNI válido (7-8 dígitos)</div>
            </div>
            
            <div class="col-12">
              <label for="telefono" class="form-label">
                <i class="fas fa-phone me-2"></i>Teléfono
              </label>
              <input type="tel" name="telefono" id="telefono" class="form-control form-control-lg" 
                     placeholder="+54 9 11 1234-5678">
            </div>
            
            <div class="col-12">
              <label for="preferencias" class="form-label">
                <i class="fas fa-cog me-2"></i>Preferencias (JSON)
              </label>
              <textarea name="preferencias" id="preferencias" class="form-control form-control-lg" 
                        rows="3" placeholder='{"newsletter":true, "idioma":"es"}'></textarea>
              <small class="text-muted">Formato JSON válido (opcional)</small>
            </div>
          </div>
          
          <div class="d-grid gap-2 mt-4">
            <button type="submit" class="btn btn-admin btn-lg">
              <i class="fas fa-check-circle me-2"></i>Registrar Usuario
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
// Validación del formulario
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