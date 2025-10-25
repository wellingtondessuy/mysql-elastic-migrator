<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use File;
use Illuminate\Support\Facades\Process;
use Log;

class ExecutionPage extends Page implements HasActions
{
    use InteractsWithActions;

    protected static ?string $title = 'Migrator - Execution';

    protected string $view = 'filament.pages.execution-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Play;

    public bool $isRunning = false;

    public string $logOutput = '';

    public bool $finished = false;

    public $processId;

    const LOG_FILE_PATH = 'logs/migrator.log';

    public function updateLog(): void {
        $logFilePath = storage_path(self::LOG_FILE_PATH);

        if (File::exists($logFilePath)) {
            $this->logOutput = File::get($logFilePath);
        } else {
            $this->logOutput = 'Log file not found! Check the storage/logs path for migrator.log file!';
        }

        // $result = Process::run("ps -p {$this->processId}");
        
        // $data = [
        //     'pid_checado' => $this->processId,
        //     'comando_foi_sucesso' => $result->successful(),
        //     'exit_code' => $result->exitCode(),
        //     'output' => $result->output(),
        //     'error_output' => $result->errorOutput(),
        // ];

        // Log::error(json_encode($data));

        // Log::error('$result->output()' . $result->output());
        // Log::error('$result->errorOutput()' . $result->errorOutput());

        // Log::error('Is Running: ' . $isRunning);

        // $result = Process::run('ls /proc/' . $this->processId);

        // Log::error($result->output());
        // Log::error($result->errorOutput());

        // if (!$this->process->running()) {
        //     $this->isRunning = false;
        //     $this->finished  = true;
        // }
    }

    public function startAction(): Action
    {
        return Action::make('start')
            ->label('Iniciar Migração')
            ->icon('heroicon-o-play')
            ->outlined()
            ->action(function () {
                // TODO-wellington: adicionar verificação das informações configuradas
                // TODO-wellington: adicionar verificação das conexões

                if ($this->isRunning) {
                    Notification::make()->title('Processo já em execução.')->warning()->send();
                    return;
                }

                $logFilePath = storage_path(self::LOG_FILE_PATH);

                if (File::exists($logFilePath)) {
                    File::delete($logFilePath);
                }

                $this->isRunning = true;

                $process = Process::path(base_path())->start('php artisan schedule:run');

                $this->processId = $process->id();
            });
    }
}
