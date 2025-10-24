<?php

namespace App\Filament\Resources\Queries\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class QueryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('index_name')
                    ->label('ElasticSearch Index Name')
                    ->required(),

                TextInput::make('document_identifier')
                    ->label('Documents Unique Identifier'),
                
                TextInput::make('content')
                    ->label('Query')
                    ->required(),
            ]);
    }
}
