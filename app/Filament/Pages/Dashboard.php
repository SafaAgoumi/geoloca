<?php

namespace App\Filament\Pages;

use App\Models\Entreprise;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Filament\Support\Colors\Color;

class Dashboard extends Page
{
    protected static string $view = 'filament.pages.dashboard';

    public string $search = '';
    public string $ville = '';
    public string $secteur = '';
    public string $etat = '';

    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static ?string $title = 'Carte des Entreprises';
    protected static ?string $navigationLabel = 'Dashboard';

    public function mount()
    {
        // Initialize any default values here if needed
    }

    public function getEntreprisesProperty()
    {
        return Entreprise::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nom_entreprise', 'like', "%{$this->search}%")
                      ->orWhere('code_ice', 'like', "%{$this->search}%")
                      ->orWhere('secteur', 'like', "%{$this->search}%")
                      ->orWhere('ville', 'like', "%{$this->search}%");
                });
            })
            ->when($this->ville, fn ($q) => $q->where('ville', 'like', "%{$this->ville}%"))
            ->when($this->secteur, fn ($q) => $q->where('secteur', 'like', "%{$this->secteur}%"))
            ->when($this->etat, fn ($q) => $q->where('en_activite', $this->etat))
            ->orderBy('nom_entreprise')
            ->get();
    }

    public function getMoroccanCitiesProperty()
    {
        return [
            'Agadir', 'Agdz', 'Aïn Harrouda', 'Aïn Leuh', 'Aïn Taoujdate', 'Aknoul',
            'Al Hoceima', 'Al Jadida', 'Aourir', 'Arfoud', 'Asilah', 'Azemmour',
            'Azilal', 'Azrou', 'Beni Mellal', 'Benslimane', 'Berkane', 'Berrechid',
            'Boujdour', 'Boulemane', 'Bouznika', 'Casablanca', 'Chefchaouen', 'Chichaoua',
            'Dakhla', 'Demnate', 'Driouch', 'El Hajeb', 'El Hoceima', 'El Jadida',
            'El Kelaa des Sraghna', 'El Ksiba', 'Errachidia', 'Essaouira', 'Fès',
            'Figuig', 'Fnideq', 'Fquih Ben Salah', 'Goulmima', 'Guelmim', 'Guercif',
            'Ifrane', 'Imintanoute', 'Inezgane', 'Jerada', 'Kénitra', 'Khénifra',
            'Khouribga', 'Ksar el Kebir', 'Laâyoune', 'Larache', 'Marrakech',
            'Mechraa Bel Ksiri', 'Meknès', 'Midelt', 'Mirleft', 'Mohammedia',
            'Nador', 'Ouarzazate', 'Ouazzane', 'Oued Zem', 'Oujda', 'Oulmes',
            'Rabat', 'Rissani', 'Safi', 'Salé', 'Sefrou', 'Settat', 'Sidi Bennour',
            'Sidi Ifni', 'Sidi Kacem', 'Sidi Slimane', 'Smara', 'Souk Larbaa',
            'Tan-Tan', 'Tanger', 'Taounate', 'Tarfaya', 'Taroudannt', 'Tata',
            'Taza', 'Témara', 'Tétouan', 'Tiznit', 'Tiflet', 'Tinghir',
            'Youssoufia', 'Zagora', 'Zaïo',
        ];
    }

    public function getAvailableVillesProperty()
    {
        return Entreprise::whereNotNull('ville')
            ->distinct()
            ->pluck('ville')
            ->sort()
            ->values();
    }

    public function getAvailableSecteursProperty()
    {
        return Entreprise::whereNotNull('secteur')
            ->distinct()
            ->pluck('secteur')
            ->sort()
            ->values();
    }

    protected function getViewData(): array
    {
        $mapData = $this->entreprises
            ->filter(fn($e) => !is_null($e->latitude) && !is_null($e->longitude))
            ->map(function ($e) {
                return [
                    'id' => $e->id,
                    'nom' => $e->nom_entreprise,
                    'ville' => $e->ville,
                    'secteur' => $e->secteur,
                    'lat' => (float)$e->latitude,
                    'lng' => (float)$e->longitude,
                    'en_activite' => $e->en_activite,
                ];
            })->values();

        return [
            'entreprises' => $this->entreprises,
            'mapData' => $mapData,
            'moroccanCities' => $this->moroccanCities,
            'availableVilles' => $this->availableVilles,
            'availableSecteurs' => $this->availableSecteurs,
        ];
    }

    public function applyFilters()
    {
        $this->updateMap();
        
        Notification::make()
            ->title('Filtres appliqués avec succès')
            ->success()
            ->duration(3000)
            ->send();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->ville = '';
        $this->secteur = '';
        $this->etat = '';
        $this->updateMap();
        
        Notification::make()
            ->title('Filtres réinitialisés')
            ->success()
            ->duration(3000)
            ->send();
    }

    public function createNewEntity()
    {
        try {
            return redirect()->route('filament.admin.resources.entreprises.create');
        } catch (\Exception $e) {
            Notification::make()
                ->title('Erreur')
                ->body('Impossible de rediriger vers la page de création.')
                ->danger()
                ->send();
        }
    }

    public function editEntreprise($entrepriseId)
    {
        try {
            return redirect()->route('filament.admin.resources.entreprises.edit', ['record' => $entrepriseId]);
        } catch (\Exception $e) {
            Notification::make()
                ->title('Erreur')
                ->body('Impossible de rediriger vers la page d\'édition.')
                ->danger()
                ->send();
        }
    }

    public function deleteEntreprise($entrepriseId)
    {
        try {
            $entreprise = Entreprise::findOrFail($entrepriseId);
            $entreprise->delete();
            
            $this->updateMap();
            
            Notification::make()
                ->title('Entreprise supprimée')
                ->body("L'entreprise {$entreprise->nom_entreprise} a été supprimée avec succès.")
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Erreur')
                ->body('Impossible de supprimer l\'entreprise.')
                ->danger()
                ->send();
        }
    }

    public function flyToMarker($entrepriseId)
    {
        $this->dispatch('flyToMarker', entrepriseId: $entrepriseId);
    }

    private function updateMap()
    {
        $this->dispatch('updateMap', mapData: $this->getViewData()['mapData']);
    }

    public function updated($propertyName)
    {
        // Auto-update map when filters change
        if (in_array($propertyName, ['search', 'ville', 'secteur', 'etat'])) {
            $this->updateMap();
        }
    }

    protected function getListeners()
    {
        return [
            'refreshMap' => 'updateMap',
        ];
    }

    // Actions pour la barre d'outils Filament
    protected function getHeaderActions(): array
    {
        return [
            Action::make('nouvelle_entreprise')
                ->label('Nouvelle Entreprise')
                ->icon('heroicon-o-plus')
                ->color('warning') // Couleur orange
                ->action(fn () => $this->createNewEntity()),
            
            Action::make('reinitialiser')
                ->label('Réinitialiser filtres')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->action(fn () => $this->resetFilters()),
            
            Action::make('exporter')
                ->label('Exporter')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('warning')
                ->action(fn () => $this->exportData()),
        ];
    }

    public function exportData()
    {
        // Logique d'export (CSV, Excel, etc.)
        Notification::make()
            ->title('Export en cours')
            ->body('Les données sont en cours d\'export...')
            ->info()
            ->send();
    }

    // Personnalisation des couleurs pour le thème orange
    protected function getThemeColors(): array
    {
        return [
            'primary' => Color::Orange,
            'danger' => Color::Red,
            'gray' => Color::Gray,
            'info' => Color::Blue,
            'success' => Color::Green,
            'warning' => Color::Amber,
        ];
    }}