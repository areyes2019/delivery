<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Panel Administrador · Delivery Celaya')</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Fuente -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/dashboard.css','resources/js/app.js'])
</head>

<body class="admin-body">

    <!-- NAVBAR -->
    <div class="topbar">
        <div class="brand">
            <i class="bi bi-taxi-front-fill"></i>
            Delivery Celaya · Administrador
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="logout-btn">
                <i class="bi bi-box-arrow-right"></i>
                Cerrar sesión
            </button>
        </form>
    </div>

    <!-- CONTENIDO DINÁMICO -->
    <div class="dashboard-container">
        @yield('content')
    </div>

</body>
</html>
