<?php

namespace App\Filament\Widgets;

use App\Models\Report;
use Cheesegrits\FilamentGoogleMaps\Actions\GoToAction;
use Cheesegrits\FilamentGoogleMaps\Actions\RadiusAction;
use Cheesegrits\FilamentGoogleMaps\Columns\MapColumn;
use Cheesegrits\FilamentGoogleMaps\Filters\MapIsFilter;
use Cheesegrits\FilamentGoogleMaps\Filters\RadiusFilter;
use Cheesegrits\FilamentGoogleMaps\Widgets\MapTableWidget;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class HeatMapTable extends MapTableWidget
{
    protected static ?string $heading = 'Reports';

    protected static ?int $sort = 1;

    protected static ?string $pollingInterval = null;

    protected static ?bool $clustering = true;

    protected static ?string $mapId = 'incidents';

    protected function getTableQuery(): Builder
    {
        return \App\Models\Report::query()->latest();
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('report_id')
                ->sortable(),

            Tables\Columns\TextColumn::make('category')
                ->getStateUsing(function (Report $record) {
                    return \Str::title(str_replace('_', ' ', $record->category));
                })
                ->sortable(),
            Tables\Columns\TextColumn::make('title')
                ->wrap()
                ->sortable(),
            Tables\Columns\TextColumn::make('created_at')
                ->sortable()
                ->dateTime(),
            Tables\Columns\TextColumn::make('latitude'),
            Tables\Columns\TextColumn::make('longitude'),
        ];
    }

    protected function getTableFilters(): array
    {
        return [

        ];
    }

    protected function getTableActions(): array
    {
        return [
            // Tables\Actions\ViewAction::make(),
            // Tables\Actions\EditAction::make(),
            GoToAction::make()
                ->zoom(14),
            RadiusAction::make(),
        ];
    }

    protected function getData(): array
    {
        $locations = $this->getRecords();

        $data = [];

        foreach ($locations as $location) {
            $data[] = [
                'location' => [
                    'lat' => $location->latitude ? round(floatval($location->latitude), static::$precision) : 0,
                    'lng' => $location->longitude ? round(floatval($location->longitude), static::$precision) : 0,
                ],
                'id' => $location->id,
            ];
        }

        return $data;
    }
}
