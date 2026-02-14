<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Administrador - Entradas Online</title>
  
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- Bootstrap 5.3.6 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  
  <style>
    :root {
      --primary: #6366f1;
      --primary-dark: #4f46e5;
      --dark: #1e293b;
      --darker: #0f172a;
    }
    
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
    }
    
    .header-gradient {
      background: linear-gradient(135deg, var(--darker) 0%, #1e293b 100%);
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .logo-title {
      font-size: 1.5rem;
      font-weight: 700;
      color: var(--white);
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    
    .nav-link-admin {
      color: var(--white);
      font-weight: 500;
      padding: 8px 16px;
      border-radius: 8px;
      transition: all 0.3s ease;
    }
    
    .nav-link-admin:hover {
      background: rgba(255, 255, 255, 0.1);
      transform: translateY(-2px);
    }
    
    .btn-admin {
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 8px;
      font-weight: 600;
      transition: all 0.3s ease;
      box-shadow: 0 4px 6px rgba(99, 102, 241, 0.3);
    }
    
    .btn-admin:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 16px rgba(99, 102, 241, 0.4);
    }
    
    .btn-outline-admin {
      color: white;
      border-color: white;
      border-radius: 8px;
      padding: 10px 20px;
      font-weight: 600;
      transition: all 0.3s ease;
    }
    
    .btn-outline-admin:hover {
      background: rgba(255, 255, 255, 0.2);
      transform: translateY(-2px);
    }
  </style>
</head>
<body>

<header class="header-gradient py-3">
  <div class="container d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center gap-3">
      <h1 class="logo-title mb-0">Sistema Administrador</h1>
      <span class="badge bg-success">v1.0</span>
    </div>
    <nav>
      <a href="views/alta.php" class="btn btn-outline-admin me-2">
        <i class="fas fa-user-plus me-1"></i> Alta de Usuario
      </a>
      <a href="views/listado.php" class="btn btn-outline-admin">
        <i class="fas fa-list me-1"></i> Listado de Usuarios
      </a>
    </nav>
  </div>
</header>

<main class="container my-5">
  <!-- El contenido se incluirá aquí -->