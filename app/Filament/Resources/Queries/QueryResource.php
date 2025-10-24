<?php

namespace App\Filament\Resources\Queries;

use App\Filament\Resources\Queries\Pages\CreateQuery;
use App\Filament\Resources\Queries\Pages\EditQuery;
use App\Filament\Resources\Queries\Pages\ListQueries;
use App\Filament\Resources\Queries\Schemas\QueryForm;
use App\Filament\Resources\Queries\Tables\QueriesTable;
use App\Models\Query;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;


class QueryResource extends Resource
{
    protected static ?string $model = Query::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'index_name';

    public static function form(Schema $schema): Schema
    {
        return QueryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QueriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListQueries::route('/'),
            'create' => CreateQuery::route('/create'),
            'edit'   => EditQuery::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['index_name', 'document_identifier', 'content'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string | Htmlable
    {
        return view('filament.general.global-search.query', [
            'index_name'          => $record->index_name,
            'document_identifier' => $record->document_identifier,
            'content'             => substr($record->content, 0, 50),
        ]);
    }
}
