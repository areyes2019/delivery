<?php

namespace App\Enums;

/**
 * 📦 Estados oficiales de una entrega (FUENTE ÚNICA DE VERDAD)
 *
 * Flujo normal:
 * CREATED → ACCEPTED → PICKED_UP → PAID → DELIVERED
 *
 * Estados terminales:
 * CANCELED
 *
 * ⚠️ REGLAS IMPORTANTES:
 * - Todos los modelos, controllers y servicios DEBEN usar este enum
 * - NO usar strings mágicos como 'EN_CAMINO', 'PAGADA', etc.
 * - El frontend solo compara contra estos valores (string)
 */
enum EntregaStatus: string
{
    /**
     * 🆕 Solicitud creada por el despachador
     * Visible en el tablero del driver
     */
    case CREATED = 'CREATED';

    /**
     * ✅ Aceptada por un driver
     * Driver asignado, aún no inicia el viaje
     */
    case ACCEPTED = 'ACCEPTED';

    /**
     * 🚚 Driver en camino / paquete recogido
     * (equivalente a EN_CAMINO)
     */
    case PICKED_UP = 'PICKED_UP';

    /**
     * 💰 Entrega cobrada
     */
    case PAID = 'PAID';

    /**
     * 📦 Entrega completada
     */
    case DELIVERED = 'DELIVERED';

    /**
     * ❌ Entrega cancelada antes de finalizar
     */
    case CANCELED = 'CANCELED';
}
