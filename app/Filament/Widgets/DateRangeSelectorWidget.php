<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class DateRangeSelectorWidget extends Widget
{
    protected static string $view = 'filament.widgets.date-range-selector-widget';

    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 40; // DateRangeSelectorWidget
}
