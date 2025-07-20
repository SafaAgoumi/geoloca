<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Field;

class LeafletMap extends Field
{
    protected string $view = 'forms.components.leaflet-map';

    protected function setUp(): void
    {
        parent::setUp();
        
        // Set default coordinates (Casablanca)
        $this->default(['lat' => 33.5731, 'lng' => -7.5898]);
    }

    public function getInitialMapState(): array
    {
        $state = $this->getState();
        
        if (is_array($state) && isset($state['lat']) && isset($state['lng'])) {
            return [
                'lat' => (float) $state['lat'],
                'lng' => (float) $state['lng']
            ];
        }
        
        return ['lat' => 33.5731, 'lng' => -7.5898]; // Default to Casablanca
    }
}