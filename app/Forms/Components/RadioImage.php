<?php

namespace App\Forms\Components;

use Closure;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Radio;
use Illuminate\Contracts\Support\Arrayable;

class RadioImage extends Radio
{
    protected string $view = 'forms.components.radio-image';
}
