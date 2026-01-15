<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Dashboard</title>

  <script>
    window.AUTH_USER = @json(auth()->user());
  </script>

  @vite(['resources/js/app.js'])

  <script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBzFG-kuEQGNxOPKf9d0-JA0CJ8ymEVyvs&libraries=places"
    defer>
  </script>
</head>
<body>
  <div id="app"></div>
</body>
</html>
