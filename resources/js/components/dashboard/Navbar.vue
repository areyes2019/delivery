<template>
  <nav class="navbar px-3 my-nav">
    <div class="container-fluid">

      <!-- IZQUIERDA -->
      <div class="d-flex align-items-center gap-3">

        <!-- CONFIG -->
        <div class="dropdown">
          <button
            class="btn btn-dark rounded-pill border-0"
            data-bs-toggle="dropdown"
            aria-expanded="false"
          >
            <i class="bi bi-gear-fill"></i>
          </button>

          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#">Perfil</a></li>
            <li><a class="dropdown-item" href="#">Usuarios</a></li>
            <li><a class="dropdown-item" href="#">Rutas</a></li>
            <li><a class="dropdown-item" href="#">Configuración</a></li>
          </ul>
        </div>

        <!-- USUARIO -->
        <div class="d-flex align-items-center">
          <strong class="text-white">{{ userName }}</strong>
          <span class="ms-2 badge bg-secondary">{{ role }}</span>
        </div>

      </div>

      <!-- DERECHA -->
      <div class="d-flex gap-2">
        <button
          class="btn btn-warning rounded-pill px-4 border-0"
          @click="$emit('nuevo-envio')"
        >
          Nuevo Envío
        </button>

        <button class="btn btn-danger rounded-pill px-4" @click="logout">
          Salir
        </button>
      </div>

    </div>
  </nav>
</template>

<script>
export default {
  name: 'Navbar',
  emits: ['nuevo-envio','logout'],
  data() {
    return {
      userName: 'Abdias Reyes',
      role: 'Admin'
    }
  },
  methods:{
    logout(){
      this.$emit('logout')
    }
  }
}
</script>
