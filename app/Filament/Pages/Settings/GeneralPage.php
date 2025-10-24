<?php

namespace App\Filament\Pages\Settings;

use Filament\Pages\Page;
use UnitEnum;

class GeneralPage extends Page
{
    protected static ?string $title = 'Settings - General';

    protected static ?string $navigationLabel = 'General';
    
    protected static UnitEnum | string | null $navigationGroup = 'Settings';

    protected string $view = 'filament.pages.settings.general-page';

    protected static ?int $navigationSort = 2;
}
