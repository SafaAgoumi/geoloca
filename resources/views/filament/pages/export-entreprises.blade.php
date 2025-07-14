<x-filament-panels::page>
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-2">Configuration de l'export</h2>
                <p class="text-sm text-gray-600">Sélectionnez les données à exporter et configurez les filtres selon vos besoins.</p>
                <br>
            </div>

            <form wire:submit="exportData">
                <x-filament-panels::form wire:submit="exportData">
                    {{ $this->form }}
                </x-filament-panels::form>

                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <div class="text-sm text-gray-500">
                            <span class="font-medium">Note:</span> L'export sera généré avec les filtres sélectionnés.
                            <br>
                        </div>
                    </div>
                    
                    <div class="flex space-x-8">
                        @foreach($this->getHeaderActions() as $action)
                            {{ $action }}
                        @endforeach
                    </div>
                </div>
                
            </form>
        </div>

        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Informations sur l'export</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <ul class="list-disc list-inside space-y-1">
                            <li>CSV: Compatible avec Excel et autres tableurs</li>
                            <li>Excel: Format .xlsx avec formatage avancé</li>
                            <li>PDF: Format imprimable pour rapports</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>