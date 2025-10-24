<?php

namespace App\Filament\Pages\Settings;

use Filament\Pages\Page;
use UnitEnum;

class ElasticSearchPage extends Page
{
    protected static ?string $title = 'Settings - Elasticsearch';

    protected static ?string $navigationLabel = 'ElasticSearch';

    protected static UnitEnum | string | null $navigationGroup = 'Settings';
    
    protected string $view = 'filament.pages.settings.elastic-search-page';

    protected static ?int $navigationSort = 1;
}
