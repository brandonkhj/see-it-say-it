<?php

namespace App\Filament\Widgets;

use Webbingbrasil\FilamentMaps\Actions;
use Webbingbrasil\FilamentMaps\Circle;
use Webbingbrasil\FilamentMaps\Marker;
use Webbingbrasil\FilamentMaps\Widgets\MapWidget;

class Map extends MapWidget
{
    protected int|string|array $columnSpan = 2;

    protected bool $hasBorder = false;

    public function setUp(): void
    {
        $this
            ->fitBounds([
                [6.722227, 99.821662],
                [0.48780536604028557, 114.61134466491899],
            ]);
    }

    public function getMarkers(): array
    {
        return [
            Marker::make('pos2')->lat(4.2105)->lng(101.9758)->popup('Hello!'),
        ];
    }

    public function getActions(): array
    {
        return [
            Actions\ZoomAction::make(),
            Actions\CenterMapAction::make()
                ->fitBounds([
                    [6.722227, 99.821662],
                    [0.48780536604028557, 114.61134466491899],
                ]),

        ];
    }

    public function getPolylines(): array
    {
        return [

        ];
    }

    public function getCircles(): array
    {
        return [
            Circle::make('circle')
                ->lat(4.2105)
                ->lng(101.9758)
                ->options(['radius' => 200000])
                ->popup('Hello!')
                ->tooltip('test2'),
        ];
    }
}
