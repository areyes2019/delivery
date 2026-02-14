@extends('layouts.admin')

@section('title', 'Flotillas')

@section('content')

    <!-- ENCABEZADO -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Flotillas</h2>
            <p class="text-muted mb-0">Administración general de flotillas registradas.</p>
        </div>

        <button class="btn btn-warning fw-semibold"
                data-bs-toggle="modal"
                data-bs-target="#modalNuevaFlotilla">
            <i class="bi bi-plus-lg"></i>
            Nueva flotilla
        </button>
    </div>

    <!-- TABLA -->
    <div class="admin-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Cliente</th>
                        <th>Despachador</th>
                        <th>Drivers</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>

                    <tr>
                        <td>1</td>
                        <td>Flotilla Centro</td>
                        <td>Restaurante La Esquina</td>
                        <td>Juan Pérez</td>
                        <td>12</td>
                        <td>
                            <span class="badge bg-success">Activa</span>
                        </td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-x-circle"></i>
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td>2</td>
                        <td>Flotilla Norte</td>
                        <td>Farmacia Express</td>
                        <td>María López</td>
                        <td>8</td>
                        <td>
                            <span class="badge bg-success">Activa</span>
                        </td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-x-circle"></i>
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td>3</td>
                        <td>Flotilla Sur</td>
                        <td>Tienda El Ahorro</td>
                        <td>Carlos Mendoza</td>
                        <td>5</td>
                        <td>
                            <span class="badge bg-danger">Inactiva</span>
                        </td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-success">
                                <i class="bi bi-check-circle"></i>
                            </button>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>
    <!-- MODAL NUEVA FLOTILLA -->
    <div class="modal fade" id="modalNuevaFlotilla" tabindex="-1">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content modal-no-radius">

                <div class="modal-header border-0 pb-2">
                    <h5 class="modal-title fw-semibold">
                        Nueva Flotilla
                    </h5>
                    <button type="button" class="btn-close"
                            data-bs-dismiss="modal"></button>
                </div>

                <form>
                    <div class="modal-body pt-0">

                        <div class="mb-3">
                            <label class="form-label">Nombre</label>
                            <input type="text"
                                class="form-control"
                                placeholder="Ej. Flotilla Centro">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Cliente</label>
                            <input type="text"
                                class="form-control"
                                placeholder="Ej. Restaurante XYZ">
                        </div>

                    </div>

                    <div class="modal-footer border-0">
                        <button type="button"
                                class="btn btn-light"
                                data-bs-dismiss="modal">
                            Cancelar
                        </button>

                        <button type="submit"
                                class="btn btn-warning fw-semibold">
                            Guardar
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

@endsection
