<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Field;

class LeafletMap extends Field
{
    protected string $view = 'forms.components.leaflet-map';

    protected function setUp(): void
    {
        parent::setUp();

        $this->afterStateHydrated(function (LeafletMap $component, $state) {
            if (!is_array($state)) {
                $component->state([
                    'lat' => 31.63,
                    'lng' => -8.0,
                ]);
            }
        });

        $this->dehydrateStateUsing(fn ($state) => $state);
    }
}
