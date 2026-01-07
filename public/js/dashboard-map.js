let map;
let markers = [];
let pollingInterval = null;

function initMap() {
  map = new google.maps.Map(document.getElementById("map"), {
    center: { lat: 20.5222, lng: -100.8123 }, // Celaya
    zoom: 13,
  });

  loadDrivers();

  // 🔁 polling cada 15 segundos
  pollingInterval = setInterval(loadDrivers, 15000);
}

function loadDrivers() {
  fetch("/api/dashboard/map", {
    headers: {
      "Accept": "application/json",
    },
  })
    .then(res => {
      if (!res.ok) throw new Error("Error API");
      return res.json();
    })
    .then(data => {
      clearMarkers();

      data.forEach(driver => {
        if (!driver.lat || !driver.lng) return;

        const marker = new google.maps.Marker({
          position: {
            lat: parseFloat(driver.lat),
            lng: parseFloat(driver.lng),
          },
          map,
          title: driver.driver_name,
          icon: getIconByStatus(driver.estado_entrega),
        });

        markers.push(marker);
      });
    })
    .catch(err => {
      console.error("Error cargando drivers:", err);
    });
}

function clearMarkers() {
  markers.forEach(marker => marker.setMap(null));
  markers = [];
}

function getIconByStatus(status) {
  switch (status) {
    case "EN_CAMINO":
      return "http://maps.google.com/mapfiles/ms/icons/green-dot.png";
    case "ASIGNADA":
      return "http://maps.google.com/mapfiles/ms/icons/yellow-dot.png";
    default:
      return "http://maps.google.com/mapfiles/ms/icons/blue-dot.png";
  }
}
