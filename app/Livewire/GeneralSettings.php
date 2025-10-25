<?php

namespace App\Livewire;

use App\Models\Setting;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Livewire\Component;

class GeneralSettings extends Component implements HasSchemas 
{
    use InteractsWithSchemas;

    public ?array $data = [];
    
    public function mount(): void
    {
        $this->rows_per_iteration = Setting::where('key', Setting::GENERAL_ROWS_PER_ITERATION)->first()?->value;
        
        $this->form->fill([
            'rows_per_iteration' => $this->rows_per_iteration,
        ]);
    }
    
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('rows_per_iteration')
                    ->default(10000)
                    ->helperText('Number of records to process per fetch (can be adjusted for better performance). Caution: a very high value may lead to memory limit issues. Default: 10000.'),
            ])
            ->statePath('data');
    }
    
    public function save(): void
    {
        $data = $this->form->getState();

        Setting::updateOrCreate(['key' => Setting::GENERAL_ROWS_PER_ITERATION], ['value' => $data['rows_per_iteration']]);

        Notification::make()
            ->title('Configuration saved!')
            ->success()
            ->send();

        // TODO-wellington: Redirect para "home"
    }

    public function render()
    {
        return view('livewire.elastic-search-settings');
    }
}
