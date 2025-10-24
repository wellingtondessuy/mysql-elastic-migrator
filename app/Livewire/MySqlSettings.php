<?php

namespace App\Livewire;

use App\Models\Setting;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Livewire\Component;

class MySqlSettings extends Component implements HasSchemas 
{
    use InteractsWithSchemas;

    public ?array $data = [];
    
    public function mount(): void
    {
        $this->host          = Setting::where('key', 'mysql_host')->first()?->value;
        $this->port          = Setting::where('key', 'mysql_port')->first()?->value;
        $this->databaseName  = Setting::where('key', 'mysql_database')->first()?->value;
        $this->username      = Setting::where('key', 'mysql_username')->first()?->value;
        $this->password      = Setting::where('key', 'mysql_password')->first()?->value;
        
        $this->form->fill([
            'host'          => $this->host,
            'port'          => $this->port,
            'database_name' => $this->databaseName,
            'username'      => $this->username,
            'password'      => $this->password,
        ]);
    }
    
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('host')
                    ->required(),
                TextInput::make('port')
                    ->required(),
                TextInput::make('database_name')
                    ->label('Database')
                    ->required(),
                TextInput::make('username')
                    ->required(),
                TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->required(),
            ])
            ->statePath('data');
    }
    
    public function save(): void
    {
        $data = $this->form->getState();

        Setting::updateOrCreate(['key' => 'mysql_host'], ['value' => $data['host']]);
        Setting::updateOrCreate(['key' => 'mysql_port'], ['value' => $data['port']]);
        Setting::updateOrCreate(['key' => 'mysql_database'], ['value' => $data['database_name']]);
        Setting::updateOrCreate(['key' => 'mysql_username'], ['value' => $data['username']]);
        Setting::updateOrCreate(['key' => 'mysql_password'], ['value' => $data['password']]);

        Notification::make()
            ->title('Configuration saved!')
            ->success()
            ->send();

        // TODO-wellington: Redirect para "home"
    }

    public function render()
    {
        return view('livewire.mysql-settings');
    }
}
