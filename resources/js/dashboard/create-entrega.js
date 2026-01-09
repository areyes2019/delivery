// resources/js/dashboard/create-entrega.js

document.addEventListener('DOMContentLoaded', () => {

    const form = document.getElementById('createEntregaForm');
    if (!form) return;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const submitBtn = form.querySelector('.submit-btn');
        if (!submitBtn) return;

        submitBtn.disabled = true;
        submitBtn.textContent = 'Guardando...';

        // ✅ FormData DIRECTO (NO convertir a objeto)
        const formData = new FormData(form);

        try {
            // 🚫 Evitar submit si NO hay lat/lng
            const pickupLat = formData.get('pickup_position[lat]');
            const destLat   = formData.get('destination_position[lat]');

            if (!pickupLat || !destLat) {
                alert('Selecciona las direcciones desde la lista de Google');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Guardar Entrega';
                return;
            }

            // 🔐 CSRF (Sanctum)
            await axios.get('/sanctum/csrf-cookie');

            // ✅ Enviar FormData tal cual
            const response = await axios.post(
                '/api/client-requests',
                formData,
                {
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'multipart/form-data',
                    },
                }
            );

            // 🎉 ÉXITO
            alert('Entrega creada correctamente');
            form.reset();

            // Limpia lat/lng hidden
            ['pickupLat','pickupLng','destLat','destLng'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.value = '';
            });

            if (typeof toggleCreatePanel === 'function') {
                toggleCreatePanel();
            }

            // 🔜 luego: refrescar sidebar
            // addEntregaToSidebar(response.data.data);

        } catch (error) {

            // ❌ VALIDACIÓN (Laravel)
            if (error.response?.status === 422) {
                const errors = error.response.data.errors || {};
                alert(Object.values(errors).flat().join('\n'));
            }

            // ❌ OTROS ERRORES
            else if (error.response) {
                alert(error.response.data.message || 'Error al crear la entrega');
            }

            // ❌ RED
            else {
                alert('Error de conexión');
            }

        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Guardar Entrega';
        }
    });
});
