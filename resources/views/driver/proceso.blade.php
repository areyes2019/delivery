<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Seguimiento de Entrega</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body { font-family: sans-serif; background-color: #f4f4f9; display: flex; justify-content: center; padding: 20px; }
        .app-container { width: 350px; background: white; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); height: 600px; display: flex; flex-direction: column; }
        .header { background: #333; color: white; padding: 20px; text-align: center; }

        .progress-container { display: flex; justify-content: space-between; padding: 30px 20px; position: relative; }
        .progress-line { position: absolute; top: 45px; left: 40px; right: 40px; height: 4px; background: #eee; }
        .progress-line-fill {
            position: absolute;
            top: 45px;
            left: 40px;
            height: 4px;
            background: #ff5a5f;
            width: {{ [
                'CREATED' => '0%',
                'ACCEPTED' => '33%',
                'PICKED_UP' => '66%',
                'PAID' => '66%',
                'DELIVERED' => '100%'
            ][$entrega->status] ?? '0%' }};
        }

        .step { z-index: 2; text-align: center; font-size: 0.7em; color: #ccc; }
        .step.active { color: #333; font-weight: bold; }
        .circle {
            width: 30px; height: 30px; border-radius: 50%;
            background: white; border: 3px solid #eee;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 5px;
        }
        .step.active .circle,
        .step.completed .circle {
            border-color: #ff5a5f;
            background: #ff5a5f;
            color: white;
        }

        .content { flex-grow: 1; text-align: center; padding: 20px; }
        .footer { padding: 20px; }
        .btn-confirm {
            width: 100%; background: #ff5a5f; color: white;
            border: none; padding: 15px; border-radius: 12px;
            font-weight: bold; cursor: pointer;
        }
        .btn-back {
            display: block; text-align: center;
            margin-top: 15px; color: #666;
            text-decoration: none; font-size: 0.9em;
        }
        .alert { background:#ffe0e0; color:#900; padding:10px; border-radius:8px; margin-bottom:10px; }
    </style>
</head>
<body>

<div class="app-container">

    <div class="header">
        Orden #{{ $entrega->id }}
    </div>

    {{-- PROGRESO --}}
    <div class="progress-container">
        <div class="progress-line"></div>
        <div class="progress-line-fill"></div>

        @php
            $steps = ['CREATED','ACCEPTED','PICKED_UP','PAID','DELIVERED'];
        @endphp

        @foreach($steps as $i => $step)
            <div class="step
                @if($entrega->status === $step) active
                @elseif(array_search($entrega->status, $steps) > $i) completed
                @endif
            ">
                <div class="circle">{{ $i+1 }}</div>
                {{ ucfirst(strtolower(str_replace('_',' ',$step))) }}
            </div>
        @endforeach
    </div>

    <div class="content">

        @if(session('error'))
            <div class="alert">{{ session('error') }}</div>
        @endif

        <h3>{{ $entrega->destination_description }}</h3>
        <p>{{ $entrega->destinatario_nombre }}</p>

    </div>

    <div class="footer">

        {{-- CREATED --}}
        @if($entrega->puedeSerAceptada())
            <form method="POST" action="{{ url("/driver/{$entrega->id}/accept") }}">
                @csrf
                <button class="btn-confirm">Confirmar Aceptar</button>
            </form>
        @endif

        {{-- ACCEPTED --}}
        @if($entrega->puedeIniciar())
            <form method="POST" action="{{ url("/driver/{$entrega->id}/start") }}">
                @csrf
                <button class="btn-confirm">Confirmar Recogida</button>
            </form>
        @endif

        {{-- PICKED_UP --}}
        @if($entrega->puedeMarcarPagada())
            <form method="POST" action="{{ url("/driver/{$entrega->id}/pay") }}">
                @csrf
                <button class="btn-confirm">Confirmar Pago</button>
            </form>
        @endif

        {{-- FINAL --}}
        @if($entrega->puedeFinalizar())
            <form method="POST" action="{{ url("/driver/{$entrega->id}/complete") }}">
                @csrf
                <button class="btn-confirm">Finalizar Entrega</button>
            </form>
        @endif

        <a href="{{ url('/driver') }}" class="btn-back">Cancelar / Volver</a>

    </div>
</div>

</body>
</html>
