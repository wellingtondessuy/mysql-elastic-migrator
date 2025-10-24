<?php

namespace App\Filament\Pages\Settings;

use Filament\Pages\Page;
use UnitEnum;

class MysqlPage extends Page
{
    protected static ?string $title = 'Settings - MySQL';

    protected static ?string $navigationLabel = 'MySQL';

    protected static UnitEnum | string | null $navigationGroup = 'Settings';

    protected string $view = 'filament.pages.settings.mysql-page';

    protected static ?int $navigationSort = 0;
}