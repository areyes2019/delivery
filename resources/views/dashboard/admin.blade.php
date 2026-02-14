<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Administrador · Delivery Celaya</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Fuente Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/dashboard.css'])
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

    <!-- CONTENIDO -->
    <div class="dashboard-container">

        <!-- BIENVENIDA -->
        <div class="welcome-card">
            <h2>Bienvenido, {{ auth()->user()->name }}</h2>
            <p>
                Desde aquí puedes administrar flotillas, despachadores y conductores.
                Control total de tu sistema.
            </p>
        </div>

        <!-- OPCIONES -->
        <div class="row g-4">

            <div class="col-md-4">
                <div class="admin-card">
                    <i class="bi bi-truck"></i>
                    <h5>Flotillas</h5>
                    <p>Administra las flotillas activas del sistema.</p>
                    <a href="#">Ver flotillas →</a>
                </div>
            </div>

            <div class="col-md-4">
                <div class="admin-card">
                    <i class="bi bi-person-badge-fill"></i>
                    <h5>Despachadores</h5>
                    <p>Asigna o elimina despachadores por flotilla.</p>
                    <a href="#">Gestionar despachadores →</a>
                </div>
            </div>

            <div class="col-md-4">
                <div class="admin-card">
                    <i class="bi bi-people-fill"></i>
                    <h5>Drivers</h5>
                    <p>Administra conductores y asignaciones.</p>
                    <a href="#">Gestionar conductores →</a>
                </div>
            </div>

        </div>

    </div>

</body>
</html>
