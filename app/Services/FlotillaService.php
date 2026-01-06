<?php

namespace App\Services;

use App\Models\Flotilla;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class FlotillaService
{
    /**
     * 📄 Listar flotillas del cliente (Admin Cliente)
     */
    public function listByCliente(User $auth)
    {
        if (!$auth->isAdminCliente()) {
            throw new HttpException(403, 'No autorizado');
        }

        return Flotilla::where('cliente_id', $auth->cliente_id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * ➕ Crear flotilla (solo Admin Cliente)
     */
    public function create(array $data, User $auth): Flotilla
    {
        if (!$auth->isAdminCliente()) {
            throw new HttpException(403, 'No autorizado');
        }

        return Flotilla::create([
            'cliente_id' => $auth->cliente_id,
            'nombre'     => $data['nombre'],
            'activo'     => true,
        ]);
    }

    /**
     * 🔒 Activar / Desactivar flotilla
     */
    public function toggleStatus(int $id, User $auth): Flotilla
    {
        if (!$auth->isAdminCliente()) {
            throw new HttpException(403, 'No autorizado');
        }

        $flotilla = Flotilla::where('id', $id)
            ->where('cliente_id', $auth->cliente_id)
            ->firstOrFail();

        $flotilla->update([
            'activo' => !$flotilla->activo
        ]);

        return $flotilla;
    }
}
