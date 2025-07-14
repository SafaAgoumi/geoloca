<?php

namespace App\Filament\Pages;

use App\Models\Entreprise;
use Filament\Pages\Page;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportEntreprises extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.pages.export-entreprises';
    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-tray';
    protected static ?string $title = 'Export des Entreprises';
    protected static ?string $navigationLabel = 'Export';
    protected static ?string $navigationGroup = 'Gestion';

    public ?array $data = [];
    
  public function mount(): void
{
    try {
        $this->form->fill([
            'export_format' => 'csv',
            'fields' => [
                'nom_entreprise',
                'code_ice',
                'ville',
                'secteur',
                'en_activite',
                'email',
                'tel'
            ],
            'filter_active_only' => false,
            'include_coordinates' => false,
        ]);
    } catch (\Exception $e) {
        Notification::make()
            ->title('Erreur lors de l\'initialisation')
            ->body('Une erreur s\'est produite lors du chargement du formulaire.')
            ->danger()
            ->send();
    }
}


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Format d\'export')
                    ->schema([
                        Select::make('export_format')
                            ->label('Format de fichier')
                            ->options([
                                'csv' => 'CSV',
                                'excel' => 'Excel (.xlsx)',
                                'pdf' => 'PDF',
                            ])
                            ->default('csv')
                            ->required(),
                    ]),

                Section::make('Champs à exporter')
                    ->schema([
                        CheckboxList::make('fields')
                            ->label('Sélectionner les champs')
                            ->options([
                                'nom_entreprise' => 'Nom de l\'entreprise',
                                'code_ice' => 'Code ICE',
                                'forme_juridique' => 'Forme juridique',
                                'type' => 'Type (PP/PM)',
                                'taille_entreprise' => 'Taille entreprise',
                                'en_activite' => 'État d\'activité',
                                'adresse' => 'Adresse',
                                'ville' => 'Ville',
                                'secteur' => 'Secteur',
                                'activite' => 'Activité',
                                'certifications' => 'Certifications',
                                'email' => 'Email',
                                'tel' => 'Téléphone',
                                'fax' => 'Fax',
                                'contact' => 'Contact',
                                'site_web' => 'Site web',
                                'if' => 'CNSS',
                                'patente' => 'Patente',
                                'date_creation' => 'Date de création',
                            ])
                            ->columns(2)
                            ->required(),

                        Toggle::make('include_coordinates')
                            ->label('Inclure les coordonnées GPS')
                            ->helperText('Latitude et longitude'),
                    ]),

                Section::make('Filtres')
                    ->schema([
                        Select::make('filter_ville')
                            ->label('Filtrer par ville')
                            ->options(function () {
                                return Entreprise::whereNotNull('ville')
                                    ->distinct()
                                    ->pluck('ville', 'ville')
                                    ->sort();
                            })
                            ->searchable()
                            ->placeholder('Toutes les villes'),

                        Select::make('filter_secteur')
                            ->label('Filtrer par secteur')
                            ->options(function () {
                                return Entreprise::whereNotNull('secteur')
                                    ->distinct()
                                    ->pluck('secteur', 'secteur')
                                    ->sort();
                            })
                            ->searchable()
                            ->placeholder('Tous les secteurs'),

                        Select::make('filter_etat')
                            ->label('Filtrer par état')
                            ->options([
                                'oui' => 'Actif uniquement',
                                'non' => 'Inactif uniquement',
                            ])
                            ->placeholder('Tous les états'),

                        Select::make('filter_type')
                            ->label('Filtrer par type')
                            ->options([
                                'PP' => 'Personne Physique',
                                'PM' => 'Personne Morale',
                            ])
                            ->placeholder('Tous les types'),

                        DatePicker::make('filter_date_from')
                            ->label('Date de création depuis')
                            ->placeholder('Date de début'),

                        DatePicker::make('filter_date_to')
                            ->label('Date de création jusqu\'à')
                            ->placeholder('Date de fin'),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('preview')
                ->label('Aperçu')
                ->icon('heroicon-o-eye')
                ->color('info')
                ->action('previewExport'),

            Action::make('export')
                ->label('Exporter')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('warning')
                ->action('exportData'),
        ];
    }

    public function previewExport(): void
    {
        $this->validate();
        
        $query = $this->buildQuery();
        $count = $query->count();
        
        Notification::make()
            ->title('Aperçu de l\'export')
            ->body("$count entreprises seront exportées avec les filtres sélectionnés.")
            ->info()
            ->send();
    }

   public function exportData(): StreamedResponse
{
    try {
        $this->validate();
        
        $format = $this->data['export_format'] ?? 'csv';
        $fields = $this->data['fields'] ?? [];
        
        if (empty($fields)) {
            Notification::make()
                ->title('Erreur')
                ->body('Veuillez sélectionner au moins un champ à exporter.')
                ->danger()
                ->send();
            
            //StreamedResponse with error content instead of JsonResponse
            return new StreamedResponse(function () {
                echo 'Erreur: Aucun champ sélectionné pour l\'export.';
            }, 400, [
                'Content-Type' => 'text/plain',
                'Content-Disposition' => 'inline',
            ]);
        }
        
        if ($this->data['include_coordinates'] ?? false) {
            $fields = array_merge($fields, ['latitude', 'longitude']);
        }
        
        $query = $this->buildQuery();
        
        switch ($format) {
            case 'csv':
                return $this->exportToCsv($query, $fields);
            case 'excel':
                return $this->exportToExcel($query, $fields);
            case 'pdf':
                return $this->exportToPdf($query, $fields);
            default:
                return $this->exportToCsv($query, $fields);
        }
    } catch (\Exception $e) {
        \Log::error('Export error: ' . $e->getMessage());
        
        Notification::make()
            ->title('Erreur lors de l\'export')
            ->body('Une erreur s\'est produite lors de l\'export: ' . $e->getMessage())
            ->danger()
            ->send();
        
        // StreamedResponse with error content instead of JsonResponse
        return new StreamedResponse(function () use ($e) {
            echo 'Erreur lors de l\'export: ' . $e->getMessage();
        }, 500, [
            'Content-Type' => 'text/plain',
            'Content-Disposition' => 'inline',
        ]);
    }
}
    
private function buildQuery()
{
    try {
        $query = Entreprise::query();
        
        // Apply filters only if they exist and are not empty
        if (!empty($this->data['filter_ville'])) {
            $query->where('ville', $this->data['filter_ville']);
        }
        
        if (!empty($this->data['filter_secteur'])) {
            $query->where('secteur', $this->data['filter_secteur']);
        }
        
        if (!empty($this->data['filter_etat'])) {
            $query->where('en_activite', $this->data['filter_etat']);
        }
        
        if (!empty($this->data['filter_type'])) {
            $query->where('type', $this->data['filter_type']);
        }
        
        if (!empty($this->data['filter_date_from'])) {
            $query->whereDate('date_creation', '>=', $this->data['filter_date_from']);
        }
        
        if (!empty($this->data['filter_date_to'])) {
            $query->whereDate('date_creation', '<=', $this->data['filter_date_to']);
        }
        
        return $query->orderBy('nom_entreprise');
    } catch (\Exception $e) {
   
        \Log::error('Error building query: ' . $e->getMessage());
        return Entreprise::query()->orderBy('nom_entreprise');
    }
}

    private function exportToCsv($query, $fields): StreamedResponse
    {
        $filename = 'entreprises_' . date('Y-m-d_H-i-s') . '.csv';
        
        return new StreamedResponse(function () use ($query, $fields) {
            $handle = fopen('php://output', 'w');
            
            
            fwrite($handle, "\xEF\xBB\xBF");
            
           
            $headers = array_map(function($field) {
                return $this->getFieldLabel($field);
            }, $fields);
            fputcsv($handle, $headers, ';');
            
           
            $query->chunk(1000, function ($entreprises) use ($handle, $fields) {
                foreach ($entreprises as $entreprise) {
                    $row = [];
                    foreach ($fields as $field) {
                        $row[] = $this->getFieldValue($entreprise, $field);
                    }
                    fputcsv($handle, $row, ';');
                }
            });
            
            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function exportToExcel($query, $fields): StreamedResponse
    {
        
        $filename = 'entreprises_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        return new StreamedResponse(function () use ($query, $fields) {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
        
            $headers = array_map(function($field) {
                return $this->getFieldLabel($field);
            }, $fields);
            
            $sheet->fromArray($headers, null, 'A1');
            
        
            $row = 2;
            $query->chunk(1000, function ($entreprises) use ($sheet, $fields, &$row) {
                foreach ($entreprises as $entreprise) {
                    $data = [];
                    foreach ($fields as $field) {
                        $data[] = $this->getFieldValue($entreprise, $field);
                    }
                    $sheet->fromArray($data, null, 'A' . $row);
                    $row++;
                }
            });
            
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

   private function exportToPdf($query, $fields): StreamedResponse
{
    $filename = 'entreprises_' . date('Y-m-d_H-i-s') . '.pdf';
    
    try {
        
        $entreprises = $query->get();
        
        // Check if we have data
        if ($entreprises->isEmpty()) {
            return new StreamedResponse(function () {
                echo 'Aucune donnée à exporter.';
            }, 400, [
                'Content-Type' => 'text/plain',
                'Content-Disposition' => 'inline',
            ]);
        }
        
        // Prepare data for the view
        $exportData = [];
        foreach ($entreprises as $entreprise) {
            $row = [];
            foreach ($fields as $field) {
                $row[$field] = $this->getFieldValue($entreprise, $field);
            }
            $exportData[] = $row;
        }
        
        // Prepare headers
        $headers = [];
        foreach ($fields as $field) {
            $headers[$field] = $this->getFieldLabel($field);
        }
        
        // Create the view
        $html = view('exports.entreprises-pdf', [
            'exportData' => $exportData,
            'headers' => $headers,
            'fields' => $fields,
            'count' => $entreprises->count(),
        ])->render();
        
        // Create PDF
        $dompdf = new \Dompdf\Dompdf([
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
        ]);
        
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        
        return new StreamedResponse(function () use ($dompdf) {
            echo $dompdf->output();
        }, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
        
    } catch (\Exception $e) {
        \Log::error('PDF Export error: ' . $e->getMessage());
        
        return new StreamedResponse(function () use ($e) {
            echo 'Erreur lors de la génération du PDF: ' . $e->getMessage();
        }, 500, [
            'Content-Type' => 'text/plain',
            'Content-Disposition' => 'inline',
        ]);
    }
}
    
 
public function getFieldLabel($field): string
{
    $labels = [
        'nom_entreprise' => 'Nom de l\'entreprise',
        'code_ice' => 'Code ICE',
        'forme_juridique' => 'Forme juridique',
        'type' => 'Type',
        'taille_entreprise' => 'Taille entreprise',
        'en_activite' => 'État d\'activité',
        'adresse' => 'Adresse',
        'ville' => 'Ville',
        'latitude' => 'Latitude',
        'longitude' => 'Longitude',
        'secteur' => 'Secteur',
        'activite' => 'Activité',
        'certifications' => 'Certifications',
        'email' => 'Email',
        'tel' => 'Téléphone',
        'fax' => 'Fax',
        'contact' => 'Contact',
        'site_web' => 'Site web',
        'if' => 'CNSS',
        'patente' => 'Patente',
        'date_creation' => 'Date de création',
    ];
    
    return $labels[$field] ?? $field;
}

public function getFieldValue($entreprise, $field)
{
    try {
        $value = data_get($entreprise, $field);
        
        // Handle null/empty values first
        if (is_null($value) || $value === '') {
            return '';
        }
        
        // Handle arrays - convert to string immediately
        if (is_array($value)) {
            $filtered = array_filter($value, function($item) {
                return !is_null($item) && $item !== '';
            });
            return implode(', ', array_map('strval', $filtered));
        }
        
        // Handle collections
        if ($value instanceof \Illuminate\Support\Collection) {
            return $value->filter(function($item) {
                return !is_null($item) && $item !== '';
            })->map(function($item) {
                return is_object($item) ? ($item->name ?? $item->title ?? $item->nom ?? strval($item)) : strval($item);
            })->implode(', ');
        }
        
        // Handle Eloquent relationships/objects
        if (is_object($value)) {
            // Handle Eloquent collections from relationships
            if (method_exists($value, 'pluck')) {
                return $value->pluck('name')->filter()->implode(', ');
            }
            
            // Handle single Eloquent model
            if (method_exists($value, 'getAttribute')) {
                return $value->name ?? $value->title ?? $value->nom ?? strval($value);
            }
            
            // Handle Carbon dates
            if ($value instanceof \Carbon\Carbon) {
                return $value->format('d/m/Y');
            }
            
            // Handle DateTime objects
            if ($value instanceof \DateTime) {
                return $value->format('d/m/Y');
            }
            
            // Handle other objects
            return strval($value);
        }
        
        // Handle boolean values
        if (is_bool($value)) {
            return $value ? 'Oui' : 'Non';
        }
        
        // Handle numeric values
        if (is_numeric($value)) {
            return strval($value);
        }
        
        // Final fallback - ensure it's a string
        return strval($value);
        
    } catch (\Exception $e) {
        // If anything goes wrong, return empty string
        return '';
    }
}

//validation rules
protected function getValidationRules(): array
{
    return [
        'data.export_format' => ['required', 'in:csv,excel,pdf'],
        'data.fields' => ['required', 'array', 'min:1'],
        'data.fields.*' => ['string'],
    ];
}


}