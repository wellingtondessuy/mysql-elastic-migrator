<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class ExecutionPage extends Page
{
    protected static ?string $title = 'Migrator - Execution';

    protected string $view = 'filament.pages.execution-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Play;

    //x-eos-database-migration-o
}
