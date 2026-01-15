<template>
  <!-- PANEL DERECHO -->
  <div class="col-md-2 p-0">
    <div class="panel">
      <a
        href="#"
        data-bs-toggle="modal"
        data-bs-target="#modalEntrega"
        class="text-decoration-none text-dark"
      >

        <div class="card">
          <div class="card-body">

            <!-- Repartidor -->
            <div class="d-flex align-items-center mb-3">
              <img
                src="https://i.pravatar.cc/80?img=12"
                class="rounded-circle me-3"
                width="50"
                height="50"
              >
              <div>
                <strong>Repartidor</strong><br>
                <small class="text-muted">En servicio</small>
              </div>
            </div>

            <h6>Cliente</h6>
            <p class="mb-1">Carlos Gómez</p>

            <h6>Dirección</h6>
            <p class="mb-2">Calle Juárez 89</p>

            <span class="badge bg-success mb-2">Entregado</span>

            <div class="progress mt-2" style="height:6px;">
              <div class="progress-bar bg-success" style="width:100%"></div>
            </div>

            <small class="text-success mt-2 d-block">
              ✔ Repartidor llegó al destino
            </small>

          </div>
        </div>

      </a>
    </div>
  </div>

  <!-- MODAL DETALLE DE ENVIO -->
  <div class="modal fade" id="modalEntrega" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-0">

        <div class="modal-header">
          <h5 class="modal-title">Seguimiento de Entrega</h5>
          <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

          <!-- TIMELINE -->
          <ul class="timeline">
            <li>
              <span class="time">6:50</span>
              <span class="dot bg-success"></span>
              <span class="text">Solicitado</span>
            </li>
            <li>
              <span class="time">7:00</span>
              <span class="dot bg-success"></span>
              <span class="text">Llegada al domicilio</span>
            </li>
            <li>
              <span class="time">7:20</span>
              <span class="dot bg-primary"></span>
              <span class="text">En camino</span>
            </li>
          </ul>

          <hr>

          <div class="text-center">
            <strong>Tiempo restante:</strong> 5 min<br>
            <small class="text-muted">Llegada aproximada 7:50</small>
          </div>

        </div>

      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'DriverState'
}
</script>