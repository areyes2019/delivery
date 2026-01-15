<?php

namespace App\Http\Controllers;
use App\Models\ClientRequest;
use Illuminate\Http\Request;

class DriverTestController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:driver']);
    }

    public function index()
    {
        $entregas = ClientRequest::where('status', 'CREATED')->get();
        return view('driver.lista', compact('entregas'));
    }

    public function show($id)
    {
        $entrega = ClientRequest::findOrFail($id);
        $this->validarEntregaDelDriver($entrega);

        return view('driver.proceso', compact('entrega'));
    }

    public function accept($id)
    {
        $driver = auth()->user();

        if ($this->driverTieneEntregaActiva($driver->id)) {
            return back()->with('error', 'Ya tienes una entrega activa');
        }

        $entrega = ClientRequest::findOrFail($id);
        $entrega->marcarComoAceptada($driver);

        return redirect("/driver/$id");
    }

    public function start($id)
    {
        $entrega = ClientRequest::findOrFail($id);
        $this->validarEntregaDelDriver($entrega);

        $entrega->iniciarEntrega(auth()->user());
        return back();
    }

    public function pay($id)
    {
        $entrega = ClientRequest::findOrFail($id);
        $this->validarEntregaDelDriver($entrega);

        $entrega->marcarComoPagada();
        return back();
    }

    public function complete($id)
    {
        $entrega = ClientRequest::findOrFail($id);
        $this->validarEntregaDelDriver($entrega);

        $entrega->marcarComoEntregada();
        return redirect('/driver');
    }

    /* ========================= */

    private function driverTieneEntregaActiva($driverId): bool
    {
        return ClientRequest::where('driver_id', $driverId)
            ->whereIn('status', ['ACCEPTED', 'PICKED_UP', 'PAID'])
            ->exists();
    }

    private function validarEntregaDelDriver(ClientRequest $entrega): void
    {
        if ($entrega->driver_id !== auth()->id()) {
            abort(403);
        }
    }
}


