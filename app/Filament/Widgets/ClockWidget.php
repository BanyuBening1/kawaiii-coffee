<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class ClockWidget extends Widget
{
    protected string $view = 'filament.widgets.clock-widget';
    protected static ?int $sort = -1; // ← paling atas
    protected int|string|array $columnSpan = 'full';
    protected static ?string $pollingInterval = null;
}