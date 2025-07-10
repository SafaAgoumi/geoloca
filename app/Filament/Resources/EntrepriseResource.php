<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EntrepriseResource\Pages;
use App\Models\Entreprise;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use App\Forms\Components\LeafletMap;
use Illuminate\Support\Facades\Http;
use Filament\Facades\Filament;

class EntrepriseResource extends Resource
{
    protected static ?string $model = Entreprise::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nom_entreprise')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('code_ICE')
                    ->label('ICE')
                    ->maxLength(50),

                Forms\Components\TextInput::make('rc')
                    ->label('RC')
                    ->maxLength(50)
                    ->nullable(),

                Forms\Components\Select::make('forme_juridique')
                    ->label('Forme Juridique')
                    ->placeholder('selectionnez une option ')
                    ->options([
                        'SA' => 'SA',
                        'SARL' => 'SARL',
                        'SNC' => 'SNC',
                        'SCS' => 'SCS',
                        'autre' => 'Autre',
                    ]),



                Forms\Components\Select::make('type')
                    ->label('Type')
                    ->placeholder('selectionnez une option ')
                    ->options([
                        'PP' => 'PP',
                        'PM' => 'PM',
                    ]),


                Forms\Components\Select::make('taille_entreprise')
                    ->label('Taille Entreprise')
                    ->placeholder('selectionnez une option ')
                    ->options([
                        'PME' => 'PME',
                        'GE' => 'GE',
                        'SU' => 'SU',
                    ]),


                Forms\Components\Select::make('en_activite')
                    ->label('En Activité')
                    ->placeholder('selectionnez une option ')
                    ->options([
                        'oui' => 'Oui',
                        'non' => 'Non',
                    ])
                    ->default('oui')
                    ,



Forms\Components\Textarea::make('adresse')
    ->required()
    ->lazy()
    ->afterStateUpdated(function ($state, callable $set) {
        if (!$state) return;

        $response = Http::withHeaders([
            'User-Agent' => 'filament-geoloc-app/1.0',
        ])->get('https://nominatim.openstreetmap.org/search', [
            'q' => $state,
            'format' => 'json',
            'limit' => 1,
        ]);

        if ($response->successful() && count($response->json()) > 0) {
            $data = $response->json()[0];
            $lat = $data['lat'];
            $lng = $data['lon'];

            $set('latitude', $lat);
            $set('longitude', $lng);
            $set('location.lat', $lat);
            $set('location.lng', $lng);
        }
    })
    ->columnSpanFull(),





                Forms\Components\TextInput::make('ville')

                    ->maxLength(255),

           LeafletMap::make('location')
    ->label('Choisir l\'emplacement')
    ->afterStateUpdated(fn ($state, callable $set) => [
        $set('latitude', $state['lat'] ?? null),
        $set('longitude', $state['lng'] ?? null),
    ]),


                Forms\Components\TextInput::make('latitude')
                    ->label('Latitude')
                    ->numeric()
                    ->reactive()
                    ->afterStateUpdated(fn ($state, callable $set) => $set('location.lat', $state))
                    ->default(fn ($get) => $get('location.lat')),

                Forms\Components\TextInput::make('longitude')
                    ->label('Longitude')
                    ->numeric()
                    ->reactive()
                    ->afterStateUpdated(fn ($state, callable $set) => $set('location.lng', $state))
                    ->default(fn ($get) => $get('location.lng')),

                Forms\Components\Textarea::make('secteur')

                    ->columnSpanFull(),

                Forms\Components\TextInput::make('activite')

                    ->maxLength(255),

                Forms\Components\Textarea::make('certifications')
                    ->nullable()
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('email')
                    ->email()
                    ->maxLength(255)
                    ->nullable(),

                Forms\Components\TextInput::make('tel')
                    ->tel()
                    ->maxLength(20)
                    ->nullable(),

                Forms\Components\TextInput::make('fax')
                    ->maxLength(50)
                    ->nullable(),

                Forms\Components\TextInput::make('contact')
                    ->maxLength(255)
                    ->nullable(),

                Forms\Components\TextInput::make('site_web')
                    ->maxLength(255)
                    ->nullable(),

                Forms\Components\TextInput::make('cnss')
                    ->maxLength(50)
                    ->nullable(),

                Forms\Components\TextInput::make('if')
                ->label('IF')
                    ->maxLength(50)
                    ->nullable(),

                Forms\Components\TextInput::make('patente')
                    ->maxLength(50)
                    ->nullable(),

                Forms\Components\DateTimePicker::make('date_creation')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nom_entreprise')->searchable(),
                Tables\Columns\TextColumn::make('code_ice')->searchable(),
                Tables\Columns\TextColumn::make('rc')->searchable(),
                Tables\Columns\TextColumn::make('forme_juridique'),
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('taille_entreprise'),
                Tables\Columns\TextColumn::make('en_activite'),
                Tables\Columns\TextColumn::make('ville')->searchable(),
                Tables\Columns\TextColumn::make('latitude')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('longitude')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('activite')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('tel')->searchable(),
                Tables\Columns\TextColumn::make('fax')->searchable(),
                Tables\Columns\TextColumn::make('contact')->searchable(),
                Tables\Columns\TextColumn::make('site_web')->searchable(),
                Tables\Columns\TextColumn::make('cnss')->searchable(),
                Tables\Columns\TextColumn::make('if')->searchable(),
                Tables\Columns\TextColumn::make('patente')->searchable(),
                Tables\Columns\TextColumn::make('date_creation')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([

                Tables\Actions\DeleteAction::make()->label('Supprimer'),
                Tables\Actions\EditAction::make()->label('Editer'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Supprimer'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEntreprises::route('/'),
            'create' => Pages\CreateEntreprise::route('/create'),
            'edit' => Pages\EditEntreprise::route('/{record}/edit'),
        ];
    }
}