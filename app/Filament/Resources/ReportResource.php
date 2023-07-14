<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportResource\Pages;
use App\Models\Report;
use Cheesegrits\FilamentGoogleMaps\Fields\Map;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput\Mask;
use Filament\Forms\Components\Wizard;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class ReportResource extends Resource
{
    protected static ?string $model = Report::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('user_id')
                    ->default(auth()->id()),
                Wizard::make([
                    Wizard\Step::make('Criticality')
                        ->schema([
                            Radio::make('criticality')
                                ->options([
                                    'high' => 'High',
                                    'medium' => 'Medium',
                                    'low' => 'Low',
                                ])
                                ->columnSpan('full')
                                ->required(),
                        ]),
                    Wizard\Step::make('Details')
                        ->schema([
                            Forms\Components\Select::make('category')
                                ->options([
                                    'near-miss' => 'Near Miss',
                                    'property-damage' => 'Property Damage',
                                    'unsafe-act' => 'Unsafe Act',
                                    'unsafe-condition' => 'Unsafe COndition',
                                ])
                                ->required(),
                            Forms\Components\TextInput::make('contact')
                                ->mask(fn (Mask $mask) => $mask->pattern('+{601}0-000 00000'))
                                ->required(),
                            Forms\Components\TextInput::make('title')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\Textarea::make('description')
                                ->required()
                                ->maxLength(255),

                            SpatieMediaLibraryFileUpload::make('images')
                                ->multiple(),

                            Forms\Components\TextInput::make('location')
                                ->required()
                                ->maxLength(255),

                                Grid::make(2)
                                ->schema([
                                    Forms\Components\TextInput::make('latitude')
                                        ->reactive()
                                        ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                            $set('map', [
                                                'lat' => floatval($state),
                                                'lng' => floatval($get('longitude')),
                                            ]);
                                        })
                                        ->lazy(),
                                    Forms\Components\TextInput::make('longitude')
                                        ->reactive()
                                        ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                            $set('map', [
                                                'lat' => floatval($get('latitude')),
                                                'lng' => floatval($state),
                                            ]);
                                        })
                                        ->lazy(),
                                    ]),

                            Map::make('map')
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                    $set('latitude', $state['lat']);
                                    $set('longitude', $state['lng']);
                                })
                                ->geolocate()
                                ->geolocateLabel('Get Location')
                                ->autocomplete('location'),

                            Forms\Components\Checkbox::make('isAnonymous')
                                ->label('Choose to submit as anonymous'),
                        ]),
                ])->columnSpan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user_id'),
                Tables\Columns\IconColumn::make('isAnonymous')
                    ->boolean(),
                Tables\Columns\TextColumn::make('criticality'),
                Tables\Columns\TextColumn::make('category'),
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReports::route('/'),
            'create' => Pages\CreateReport::route('/create'),
            'edit' => Pages\EditReport::route('/{record}/edit'),
        ];
    }
}
