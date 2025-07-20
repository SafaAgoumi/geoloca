{{-- resources/views/forms/components/leaflet-map.blade.php --}}
<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div class="w-full">
        <div 
            wire:ignore
            x-data="leafletMapComponent(@entangle($getStatePath()).live)"
            x-init="initMap()"
            class="w-full"
        >
            <div id="map-{{ str_replace(['.', '[', ']'], '-', $getStatePath()) }}" class="w-full h-96 rounded-lg border border-gray-300"></div>
        </div>
    </div>

    @once
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

        <script>
            function leafletMapComponent(stateRef) {
                return {
                    map: null,
                    marker: null,
                    state: stateRef,

                    initMap() {
                        const lat = this.state?.lat ?? 33.5731;
                        const lng = this.state?.lng ?? -7.5898;
                        const mapId = 'map-' + Math.random().toString(36).substring(2, 10);

                        this.$el.querySelector('div[id^="map-"]').id = mapId;

                        this.map = L.map(mapId).setView([lat, lng], 13);

                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '© OpenStreetMap contributors',
                            maxZoom: 19,
                        }).addTo(this.map);

                        this.marker = L.marker([lat, lng], {
                            draggable: true,
                        }).addTo(this.map);

                        // On marker drag: update Livewire state + reverse geocode
                        this.marker.on('dragend', (e) => {
                            const pos = e.target.getLatLng();
                            this.state.lat = pos.lat;
                            this.state.lng = pos.lng;

                            this.reverseGeocode(pos.lat, pos.lng);
                        });

                        this.map.on('click', (e) => {
                            const pos = e.latlng;
                            this.marker.setLatLng(pos);
                            this.state.lat = pos.lat;
                            this.state.lng = pos.lng;

                            this.reverseGeocode(pos.lat, pos.lng);
                        });

                        // Watch Livewire location state changes
                        this.$watch('state', (newVal) => {
                            if (!newVal?.lat || !newVal?.lng || !this.marker || !this.map) return;

                            const lat = parseFloat(newVal.lat);
                            const lng = parseFloat(newVal.lng);

                            this.marker.setLatLng([lat, lng]);
                            this.map.setView([lat, lng], this.map.getZoom());
                        });
                    },

                    // 🧭 Reverse Geocode from coords → update adresse
                    reverseGeocode(lat, lng) {
                        fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`, {
                            headers: {
                                'User-Agent': 'filament-geoloc-app/1.0'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data?.display_name) {
                                // Manually set adresse field via Livewire
                                const adresseInput = document.querySelector('[wire\\:model="data.adresse"], [wire\\:model.defer="data.adresse"], textarea[name$="adresse"]');

                                if (adresseInput) {
                                    adresseInput.value = data.display_name;
                                    adresseInput.dispatchEvent(new Event('input'));
                                    adresseInput.dispatchEvent(new Event('blur')); // optional
                                }
                            }
                        })
                        .catch(err => {
                            console.error('Reverse geocoding failed', err);
                        });
                    }
                };
            }
        </script>
    @endonce
</x-dynamic-component>
