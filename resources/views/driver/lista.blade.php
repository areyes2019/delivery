<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos Disponibles</title>

    <style>
        body { font-family: sans-serif; background-color: #f4f4f9; display: flex; justify-content: center; padding: 20px; }
        .app-container { width: 350px; background: white; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); overflow: hidden; }
        .header { background: #ff5a5f; color: white; padding: 20px; text-align: center; font-weight: bold; }
        .task-list { padding: 15px; }
        .task-card { border: 1px solid #eee; border-radius: 12px; padding: 15px; margin-bottom: 15px; transition: transform 0.2s; }
        .task-card:hover { transform: scale(1.02); }
        .task-title { font-weight: bold; margin-bottom: 5px; display: block; }
        .task-info { font-size: 0.9em; color: #666; margin-bottom: 10px; }
        .btn-take { width: 100%; background: #28a745; color: white; border: none; padding: 10px; border-radius: 8px; cursor: pointer; font-weight: bold; }
        .alert { background:#ffe0e0; color:#900; padding:10px; border-radius:8px; margin-bottom:10px; }
    </style>
</head>
<body>

<div class="app-container">
    <div class="header">Pedidos Disponibles</div>

    <div class="task-list">

        {{-- Mensaje de error --}}
        @if(session('error'))
            <div class="alert">
                {{ session('error') }}
            </div>
        @endif

        {{-- Sin pedidos --}}
        @if($entregas->isEmpty())
            <p style="text-align:center; color:#777;">
                No hay pedidos disponibles
            </p>
        @endif

        {{-- Listado de pedidos --}}
        @foreach($entregas as $entrega)
            <div class="task-card">
                <span class="task-title">
                    Pedido #{{ $entrega->id }}
                    @if($entrega->destinatario_nombre)
                        - {{ $entrega->destinatario_nombre }}
                    @endif
                </span>

                <div class="task-info">
                    📍 {{ $entrega->destination_description }} <br>
                    💰 Ganancia estimada: ${{ number_format($entrega->ganancia ?? 0, 2) }}
                </div>

                <form method="POST" action="{{ url('/driver/'.$entrega->id.'/accept') }}">
                    @csrf
                    <button class="btn-take">
                        Tomar tarea
                    </button>
                </form>
            </div>
        @endforeach

    </div>
</div>

</body>
</html>
