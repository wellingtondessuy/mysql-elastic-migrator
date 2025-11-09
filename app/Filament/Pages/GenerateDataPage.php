<?php

namespace App\Filament\Pages;

use App\DatabaseInsertions\LoadDatabase;
use BackedEnum;
use Config;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use File;
use Illuminate\Support\Facades\Process;

class GenerateDataPage extends Page implements HasActions
{
    use InteractsWithActions;

    protected static ?string $title = 'Migrator - Generate Data';

    protected string $view = 'filament.pages.generate-data-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Play;

    const LOG_FILE_PATH = 'logs/storage.log';

    public bool $isRunning = false;

    public static function shouldRegisterNavigation(): bool
    {
        return Config::get('migrator.validation.generate_data_page_enabled') === true;
    }

    public function startAction(): Action
    {
        return Action::make('start')
            ->label('Start Generation')
            ->icon('heroicon-o-play')
            ->outlined()
            ->action(function () {
                if ($this->isRunning) {
                    Notification::make()->title('Generation process already running!')->warning()->send();
                    return;
                }

                $this->isRunning = true;

                $logFilePath = storage_path(self::LOG_FILE_PATH);

                if (File::exists($logFilePath)) {
                    File::delete($logFilePath);
                }

                (new LoadDatabase)->insertData();

                Notification::make()->title('Generation process finished successfully!')->success()->send();

                return redirect(ExecutionPage::getUrl());
            });
    }
}
