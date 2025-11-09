<x-filament-panels::page>

    <div>

        <x-filament::section>
            <x-slot name="heading">
                Execution Control
            </x-slot>
            <x-slot name="description">
                On this screen, you can track the data generation process via the log. Note: this process will use the same MySQL database configured to migration process and automatically will truncate the following tables: customers, product_categories, products, sales, sale_products. If you are using a MySQL database just for tests, before this process, create this same tables using sql content from <code>testing_database/schema.sql</code> file.

                <br><br>Check <code>storage/logs/laravel.log</code> file to see generation process status.
            </x-slot>
                
            {{ $this->startAction }}
        </x-filament::section>
        
    </div>

</x-filament-panels::page>