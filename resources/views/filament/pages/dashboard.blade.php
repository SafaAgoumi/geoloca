<x-filament::page>
    
    <div class="flex min-h-screen bg-gray-50">
        {{-- LEFT SIDEBAR --}}
        <div class="w-80 bg-white shadow-lg border-r border-gray-200">
            
            <div class="p-6 space-y-6">
                {{-- Search Section --}}
                <div>
                    <h2 class="text-center text-gray-700 ">Filtres de Recherche</h2>
                    <br>
                   <div class="pt-4 border-t border-gray-200">
                    <label  class="block text-sm font-medium text-gray-700 mb-2">Dénomination</label>
                    <div class="relative">
                        <input
                            type="text"
                            placeholder="Rechercher..."
                            wire:model="search"
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all"
                        />
                        </div>
                    </div>
                </div>

                {{-- Filters Section --}}
                <div class="space-y-4">
                    {{-- Ville Filter --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ville</label>
                        <div class="relative">
                            <select
                                wire:model="ville"
                                class="w-full appearance-none bg-white border border-gray-300 rounded-lg px-3 py-2.5 pr-10 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all"
                            >
                                <option value="">Toutes les villes</option>
                                @foreach($moroccanCities as $city)
                                    <option value="{{ $city }}">{{ $city }}</option>
                                @endforeach
                                @foreach($availableVilles as $villeOption)
                                    @if(!in_array($villeOption, $moroccanCities))
                                        <option value="{{ $villeOption }}">{{ $villeOption }}</option>
                                    @endif
                                @endforeach
                            </select>
                           
                        </div>
                    </div>

                    {{-- Secteur Filter --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Secteur</label>
                        <div class="relative">
                            <select
                                wire:model="secteur"
                                class="w-full appearance-none bg-white border border-gray-300 rounded-lg px-3 py-2.5 pr-10 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all"
                            >
                                <option value="">Tous les secteurs</option>
                                @foreach($availableSecteurs as $secteurOption)
                                    <option value="{{ $secteurOption }}">{{ $secteurOption }}</option>
                                @endforeach
                            </select>
                            
                        </div>
                    </div>

                    {{-- État Filter --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">État</label>
                        <div class="relative">
                            <select
                                wire:model="etat"
                                class="w-full appearance-none bg-white border border-gray-300 rounded-lg px-3 py-2.5 pr-10 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all"
                            >
                                <option value="">Tous les états</option>
                                <option value="oui">Actif</option>
                                <option value="non">Inactif</option>
                            </select>
                          
                        </div>
                    </div>

                </div>

                {{-- Filter Button --}}
                <div class="pt-4 border-t border-gray-200">
                    <button
                        wire:click="applyFilters"
                        class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2.5 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center space-x-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        <span>Filtrer</span>
                    </button>
                </div>

               
            </div>
        </div>

        {{-- RIGHT CONTENT --}}
        <div class="flex-1 flex flex-col min-w-0">
            {{-- MAP (50% HEIGHT) --}}
            <div class="flex-1 bg-white shadow-sm" wire:ignore>
                <div id="dashboardMap" class="h-full w-full"></div>
            </div>

            {{-- TABLE (50% HEIGHT) --}}
            <div class="flex-1 bg-white shadow-sm overflow-hidden">
                <div class="p-6 h-full flex flex-col">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Entreprises ({{ count($entreprises) }})
                        </h3>
                    </div>
                    
                    <div class="flex-1 overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                        <div class="h-full overflow-auto">
                            <table class="w-full divide-y divide-gray-300" style="min-width: 2000px;">
                                <thead class="bg-gray-50 sticky top-0 z-10">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                            Nom entreprise
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">
                                            ICE
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                            Forme juridique
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20">
                                            Type
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                            Taille entreprise
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">
                                            En activité
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">
                                            Adresse
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">
                                            Ville
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">
                                            Latitude
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">
                                            Longitude
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                            Secteur
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                            Activité
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                            Certifications
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">
                                            Email
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-28">
                                            Tel
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-28">
                                            Fax
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-28">
                                            Contact
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">
                                            Site Web
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20">
                                            Cnss
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20">
                                            Patente
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                            Date création
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32 sticky right-0 bg-gray-50 z-20">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($entreprises as $entreprise)
                                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $entreprise->nom_entreprise }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $entreprise->code_ice ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $entreprise->forme_juridique ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $entreprise->type ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $entreprise->taille_entreprise ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $entreprise->en_activite === 'oui' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $entreprise->en_activite === 'oui' ? 'Oui' : 'Non' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $entreprise->adresse }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $entreprise->ville ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $entreprise->latitude }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $entreprise->longitude }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $entreprise->secteur ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $entreprise->activite ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $entreprise->certifications ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $entreprise->email ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $entreprise->tel ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $entreprise->fax ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $entreprise->contact ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $entreprise->site_web  ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $entreprise->if  ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $entreprise->patente  ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $entreprise->date_creation }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium sticky right-0 bg-white z-10 shadow-lg">
                                                <div class="flex space-x-2">
                                                   
                                                    <button 
                                                        wire:click="editEntreprise({{ $entreprise->id }})"
                                                        class="text-orange-600 hover:text-orange-900 transition-colors"
                                                        title="Modifier"
                                                    >
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </button>
                                                    <button 
                                                        wire:click="deleteEntreprise({{ $entreprise->id }})"
                                                        class="text-red-600 hover:text-red-900 transition-colors"
                                                        title="Supprimer"
                                                    >
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="22" class="px-6 py-12 text-center text-gray-500">
                                                <div class="flex flex-col items-center">
                                                    
                                                    <p class="text-sm text-gray-500">Aucune entreprise trouvée</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css"
              integrity="sha256-kLaT2GOSpHechhsozzB+flnD+zUyjE2LlfWPgU04xyI="
              crossorigin=""/>
        <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"
                integrity="sha256-WBkoXOwTeyKclOHuWtc+i2uENFpDZ9YPdf5Hf+D7ewM="
                crossorigin=""></script>

        <script>
            let map;
            let markers = [];

            function initMap() {
                map = L.map('dashboardMap').setView([31.63, -8.0], 6);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                }).addTo(map);

                updateMarkers(@json($mapData));
            }

            function updateMarkers(mapData) {
                markers.forEach(marker => map.removeLayer(marker));
                markers = [];

                mapData.forEach(entreprise => {
                    const isActive = entreprise.en_activite === 'oui';
                    const marker = L.circleMarker([entreprise.lat, entreprise.lng], {
                        entrepriseId: entreprise.id,
                        radius: 8,
                        fillColor: isActive ? '#EA580C' : '#EF4444',
                        color: '#ffffff',
                        weight: 2,
                        opacity: 1,
                        fillOpacity: 0.8
                    })
                    .addTo(map)
                    .bindPopup(`
                        <div class="p-3 min-w-48">
                            <h4 class="font-semibold text-gray-900 mb-2">${entreprise.nom}</h4>
                            <div class="space-y-1 text-sm">
                                <p class="text-gray-600"><strong>Ville:</strong> ${entreprise.ville || '-'}</p>
                                <p class="text-gray-600"><strong>Secteur:</strong> ${entreprise.secteur || '-'}</p>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${isActive ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                    ${isActive ? 'Actif' : 'Inactif'}
                                </span>
                            </div>
                        </div>
                    `);
                    markers.push(marker);
                });

                if (markers.length > 0) {
                    const group = new L.featureGroup(markers);
                    map.fitBounds(group.getBounds(), { padding: [20, 20] });
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(initMap, 100);
            });

            Livewire.on('updateMap', (event) => {
                updateMarkers(event.mapData);
            });

            Livewire.on('flyToMarker', (event) => {
                const marker = markers.find(m => m.options.entrepriseId === event.entrepriseId);
                if (marker) {
                    map.flyTo(marker.getLatLng(), 15);
                    marker.openPopup();
                }
            });

            window.addEventListener('resize', function() {
                if (map) {
                    setTimeout(() => map.invalidateSize(), 100);
                }
            });
        </script>
    @endpush
</x-filament::page>