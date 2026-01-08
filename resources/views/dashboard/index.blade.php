<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <style>
        #map {
            width: 100%;
            height: 100vh;
        }
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }

        /* 🔝 NAVBAR */
        .navbar {
            height: 56px;
            background: #1f2937; /* gris oscuro */
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
        }

        .navbar .title {
            font-weight: bold;
            font-size: 16px;
        }

        .navbar .right {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .navbar .user {
            font-size: 14px;
            opacity: 0.9;
        }

        .navbar button {
            background: #ef4444;
            border: none;
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .navbar button:hover {
            background: #dc2626;
        }

        /* 🗺️ MAPA */
        #map {
            width: 100%;
            height: calc(100vh - 56px); /* resta altura del navbar */
        }
    </style>
</head>
<body>
<!-- 🔝 NAVBAR -->
<div class="navbar">
    <div class="title">🚚 Delivery Celaya</div>

    <div class="right">
        <div class="user">
            {{ auth()->user()->name }} {{ auth()->user()->lastname }}
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">Cerrar sesión</button>
        </form>
    </div>
</div>
<div class="dashboard-body">

    <!-- 🧭 PANEL LATERAL -->
    <aside class="sidebar">
        <h3>📦 Entregas</h3>

        <!-- Tabs -->
        <div class="tabs">
            <button class="tab active" data-tab="sin-asignar">Sin asignar</button>
            <button class="tab" data-tab="en-curso">En curso</button>
            <button class="tab" data-tab="terminadas">Terminadas</button>
        </div>

        <!-- Contenido tabs -->
        <div class="tab-content active" id="sin-asignar">
            <div class="card">
                <strong>#1023</strong>
                <p>Cliente: Farmacia XYZ</p>
                <span class="badge gray">Sin asignar</span>
            </div>
        </div>

        <div class="tab-content" id="en-curso">
            <div class="card">
                <strong>#1021</strong>
                <p>Driver: Juan Pérez</p>
                <span class="badge yellow">En camino</span>
            </div>
        </div>

        <div class="tab-content" id="terminadas">
            <div class="card">
                <strong>#1018</strong>
                <p>Entregada</p>
                <span class="badge green">Terminada</span>
            </div>
        </div>
    </aside>

    <!-- 🗺️ MAPA -->
    <main class="map-wrapper">
        <div id="map"></div>
    </main>

</div>


<script src="{{ asset('js/dashboard-map.js') }}"></script>
<script
  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBzFG-kuEQGNxOPKf9d0-JA0CJ8ymEVyvs&callback=initMap"
  async
  defer
></script>

</body>
</html>
