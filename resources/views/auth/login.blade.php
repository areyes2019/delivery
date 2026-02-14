<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Delivery Celaya · Iniciar sesión</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- Fuente Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  @vite(['resources/css/dashboard.css'])
</head>

<body class="login-body">

  <div class="main-card">

    <!-- LADO IZQUIERDO -->
    <div class="left-panel">
      <div class="left-overlay"></div>

      <div class="vehicle-grid">
        <div class="vehicle-item">
          <i class="bi bi-car-front-fill"></i>
          Sedán
        </div>

        <div class="vehicle-item">
          <i class="bi bi-bicycle"></i>
          Moto
        </div>

        <div class="vehicle-item">
          <i class="bi bi-taxi-front-fill"></i>
          Taxi
        </div>

        <div class="vehicle-item">
          <i class="bi bi-scooter"></i>
          Scooter
        </div>
      </div>
    </div>

    <!-- LADO DERECHO -->
    <div class="right-panel">
      <h2>Bienvenido a Delivery Celaya</h2>
      <p>
        Inicia sesión para acceder al centro de control y gestionar tu flotilla.
      </p>

      <form method="POST" action="{{ route('login') }}" class="login-form">
        @csrf

        @if ($errors->any())
          <div class="error-message">
            {{ $errors->first() }}
          </div>
        @endif

        <div class="mb-3">
          <input 
            type="email" 
            name="email" 
            class="form-control" 
            placeholder="Correo electrónico"
            value="{{ old('email') }}"
            required>
        </div>

        <div class="mb-3">
          <input 
            type="password" 
            name="password" 
            class="form-control" 
            placeholder="Contraseña"
            required>
        </div>

        <button type="submit" class="btn btn-login w-100">
          Ingresar al sistema
        </button>
      </form>

      <div class="footer-note">
        Sistema interno de gestión · Delivery Celaya
      </div>
    </div>

  </div>

</body>
</html>
