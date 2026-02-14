@extends('layouts.admin')

@section('title', 'Dashboard Administrador')

@section('content')

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
                <a href="{{ route('admin.flotillas') }}">Ver flotillas →</a>
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

@endsection
