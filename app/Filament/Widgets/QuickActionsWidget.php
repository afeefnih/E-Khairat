<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class QuickActionsWidget extends Widget
{
    protected static ?string $heading = 'Tindakan Pantas';
    protected int | string | array $columnSpan = 2;
    protected static ?int $sort = 90; // QuickActionsWidget

    protected static string $view = 'filament.widgets.quick-actions-widget';
}
