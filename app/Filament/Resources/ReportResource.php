<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportResource\Pages;
use App\Forms\Components\RadioImage;
use App\Forms\Components\Voice;
use App\Models\Report;
use Cheesegrits\FilamentGoogleMaps\Fields\Map;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Support\Str;
use Suleymanozev\FilamentRadioButtonField\Forms\Components\RadioButton;

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

                Group::make([
                    RadioImage::make('category')
                        ->options([
                            'near_miss' => 'Near Miss',
                            'property_damage' => 'Property Damage',
                            'unsafe_act' => 'Unsafe Act',
                            'unsafe_condition' => 'Unsafe Condition',
                        ])
                        ->required(),

                    // Forms\Components\Select::make('category')
                    //     ->options([
                    //         'near_miss' => 'Near Miss',
                    //         'property_damage' => 'Property Damage',
                    //         'unsafe_act' => 'Unsafe Act',
                    //         'unsafe_condition' => 'Unsafe Condition',
                    //     ])
                    //     ->required(),
                    Forms\Components\TextInput::make('title')
                        ->datalist([
                            'A close call that could have resulted in a serious accident',
                            'An incident narrowly avoided',
                            'A potential disaster averted',
                            'A near collision on the road',
                            'A close encounter with a hazardous situation',
                            'Accidental damage to a company vehicle',
                            'Vandalism causing destruction to public property',
                            'Fire damage to a residential building',
                            'Water leak causing property damage',
                            'Storm-related destruction of infrastructure',
                            'Ignoring safety procedures while operating heavy machinery',
                            'Working without wearing appropriate personal protective equipment',
                            'Engaging in horseplay in the workplace',
                            'Using a cellphone while driving',
                            'Disregarding traffic rules and speeding',
                            'Exposed electrical wires without proper insulation',
                            'Slippery floors due to a spillage',
                            'Inadequate lighting in stairwells',
                            'Faulty fire alarm system in a commercial building',
                            'Missing guardrails on elevated platforms',
                        ])
                        ->maxLength(255),
                    Forms\Components\Textarea::make('description')
                        ->maxLength(255),

                    Voice::make('voice')
                        ->dehydrated(false),

                    SpatieMediaLibraryFileUpload::make('attachments')
                        ->collection(Report::MEDIA_COLLECTION_ATTACHMENT)
                        ->enableReordering()
                        ->maxSize(10000)
                        ->multiple(),

                    Map::make('map')
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $get, callable $set) {
                            $set('latitude', $state['lat']);
                            $set('longitude', $state['lng']);
                        })
                        ->geolocate()
                        ->defaultZoom(20)
                        ->defaultLocation([1.3759147, 104.1466147])
                        ->geolocateLabel('Get Location')
                        ->autocomplete('location'),

                ])->columnSpanFull(),

                Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('latitude')
                            ->reactive()
                            ->disabled()
                            ->default(1.3759147)
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                $set('map', [
                                    'lat' => floatval($state),
                                    'lng' => floatval($get('longitude')),
                                ]);
                            })
                            ->lazy(),
                        Forms\Components\TextInput::make('longitude')
                            ->reactive()
                            ->disabled()
                            ->default(104.1466147)
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                $set('map', [
                                    'lat' => floatval($get('latitude')),
                                    'lng' => floatval($state),
                                ]);
                            })
                            ->lazy(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('report_id')
                    ->sortable(),

                Tables\Columns\TextColumn::make('category')
                    ->getStateUsing(function (Report $record) {
                        return Str::title(str_replace('_', ' ', $record->category));
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->wrap()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable()
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
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
