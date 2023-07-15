<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\HeatMap;
use Filament\Pages\Dashboard as BasePage;

class Dashboard extends BasePage
{
    protected static string $view = 'filament.pages.dashboard';

    protected static function getNavigationLabel(): string
    {
        return 'Reports Near You';
    }

    protected function getTitle(): string
    {
        return 'Reports Near You';
    }

    protected function getHeaderWidgetsColumns(): int | array
    {
        return 1;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            HeatMap::class
        ];
    }
}
