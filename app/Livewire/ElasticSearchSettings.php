<?php

namespace App\Livewire;

use App\Models\Setting;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Livewire\Component;

class ElasticSearchSettings extends Component implements HasSchemas 
{
    use InteractsWithSchemas;

    public ?array $data = [];
    
    public function mount(): void
    {
        $this->host          = Setting::where('key', Setting::ELASTICSEARCH_HOST)->first()?->value;
        $this->api_key       = Setting::where('key', Setting::ELASTICSEARCH_API_KEY)->first()?->value;
        
        $this->form->fill([
            'host'          => $this->host,
            'api_key'       => $this->api_key,
        ]);
    }
    
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('host')
                    ->required(),
                TextInput::make('api_key')
                    ->required(),
            ])
            ->statePath('data');
    }
    
    public function save(): void
    {
        $data = $this->form->getState();

        Setting::updateOrCreate(['key' => Setting::ELASTICSEARCH_HOST], ['value' => $data['host']]);
        Setting::updateOrCreate(['key' => Setting::ELASTICSEARCH_API_KEY], ['value' => $data['api_key']]);

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
