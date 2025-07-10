@php
    $mapId = 'leafletMap_' . \Illuminate\Support\Str::random(5);
@endphp

<div wire:ignore>
    <div id="{{ $mapId }}" style="height: 300px;"></div>

    <input type="hidden" wire:model.defer="{{ $getStatePath() }}.lat">
    <input type="hidden" wire:model.defer="{{ $getStatePath() }}.lng">
</div>

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let lat = @js($getState()['lat'] ?? 31.63);
            let lng = @js($getState()['lng'] ?? -8.0);

            let map = L.map('{{ $mapId }}').setView([lat, lng], 6);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap'
            }).addTo(map);

            let marker = L.marker([lat, lng], { draggable: true }).addTo(map);

            marker.on('dragend', function (e) {
                let pos = marker.getLatLng();
                @this.set('{{ $getStatePath() }}.lat', pos.lat);
                @this.set('{{ $getStatePath() }}.lng', pos.lng);
            });

            map.on('click', function (e) {
                marker.setLatLng(e.latlng);
                @this.set('{{ $getStatePath() }}.lat', e.latlng.lat);
                @this.set('{{ $getStatePath() }}.lng', e.latlng.lng);
            });

            // Pour forcer l'affichage correct de la carte après le rendu
            setTimeout(() => {
                map.invalidateSize();
            }, 300);
        });
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
@endpush