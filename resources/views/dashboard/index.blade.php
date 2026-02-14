<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Dashboard</title>

  @vite(['resources/js/app.js'])

  <script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBzFG-kuEQGNxOPKf9d0-JA0CJ8ymEVyvs&libraries=places"
    defer>
  </script>
</head>
<body>
  <body>
  <div style="position:fixed; top:20px; right:20px; z-index:999;">
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" 
        style="
          background:#000;
          color:#FFD700;
          border:none;
          padding:10px 16px;
          border-radius:8px;
          font-weight:bold;
          cursor:pointer;
        ">
        Cerrar sesión
      </button>
    </form>
  </div>
</body>

  <div id="app"></div>
</body>
</html>
