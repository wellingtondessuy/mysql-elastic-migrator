<x-filament-panels::page>

    <div @if($isRunning && !$finished) wire:poll.2s="updateLog" @endif>

        @if(!$isRunning && !$finished)
            <x-filament::section>
                <x-slot name="heading">
                    Execution Control
                </x-slot>
                <x-slot name="description">
                    On this screen, you can track the data migration process via the log.
                </x-slot>
                    
                {{ $this->startAction }}
            </x-filament::section>
        
        @else

            <x-filament::section class="mt-6">
                <x-slot name="heading">
                    Execution Log
                </x-slot>
                <x-slot name="description">
                    Check the log to see how the data migration process is going.
                </x-slot>

                <x-filament::loading-indicator class="h-5 w-5" />

                <pre class="w-full p-4 bg-gray-900 text-white rounded-lg overflow-auto" 
                    style="min-height: 300px; max-height: 600px; font-family: monospace;">
                    <code>{{ $logOutput }}</code>
                </pre>
                
            </x-filament::section>

        @endif
    </div>

</x-filament-panels::page>