<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Delivery</title>

    <style>
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
        }

        #map {
            width: 100%;
            height: 100vh;
        }
    </style>
</head>
<body>

<div id="map"></div>

<script src="{{ asset('/js/dashboard-map.js') }}"></script>

<script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBzFG-kuEQGNxOPKf9d0-JA0CJ8ymEVyvs&callback=initMap"
    async
    defer
></script>

</body>
</html>
