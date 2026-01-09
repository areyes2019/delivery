// resources/js/dashboard/places.js

window.initPlaces = function () {

    const pickupInput = document.getElementById('pickupInput');
    const destInput   = document.getElementById('destinationInput');

    // 🟢 PICKUP
    if (pickupInput && !pickupInput.dataset.placesInit) {

        const pickupAutocomplete = new google.maps.places.Autocomplete(
            pickupInput,
            { fields: ['geometry', 'formatted_address'] }
        );

        pickupAutocomplete.addListener('place_changed', () => {
            const place = pickupAutocomplete.getPlace();
            if (!place.geometry) return;

            document.getElementById('pickupLat').value =
                place.geometry.location.lat();
            document.getElementById('pickupLng').value =
                place.geometry.location.lng();
        });

        pickupInput.dataset.placesInit = 'true';
    }

    // 🟢 DESTINATION
    if (destInput && !destInput.dataset.placesInit) {

        const destAutocomplete = new google.maps.places.Autocomplete(
            destInput,
            { fields: ['geometry', 'formatted_address'] }
        );

        destAutocomplete.addListener('place_changed', () => {
            const place = destAutocomplete.getPlace();
            if (!place.geometry) return;

            document.getElementById('destLat').value =
                place.geometry.location.lat();
            document.getElementById('destLng').value =
                place.geometry.location.lng();
        });

        destInput.dataset.placesInit = 'true';
    }
};
