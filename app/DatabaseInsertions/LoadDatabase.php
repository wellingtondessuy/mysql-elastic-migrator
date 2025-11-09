<?php


namespace App\DatabaseInsertions;

use App\Migrator\QueryExecutor;
use App\Models\Setting;
use Config;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;
use Log;

class LoadDatabase
{
    const SALES_QUANTITY = 100;

    private $groups = [
        'Atacado',
        'Mercado',
        'Varejo',
        'Cliente Final'
    ];

    private $customersIds         = [];
    private $productCategoriesIds = [];
    private $productsIds          = [];

    public function insertData()
    {
        Log::info('Load Database');

        $mysqlHost     = Setting::where('key', Setting::MYSQL_HOST)->first()?->value;
        $mysqlPort     = Setting::where('key', Setting::MYSQL_PORT)->first()?->value;
        $mysqlDatabase = Setting::where('key', Setting::MYSQL_DATABASE)->first()?->value;
        $mysqlUsername = Setting::where('key', Setting::MYSQL_USERNAME)->first()?->value;
        $mysqlPassword = Setting::where('key', Setting::MYSQL_PASSWORD)->first()?->value;

        $fromMysqlDatabaseConfig = [
            'driver'    => 'mysql',
            'host'      => $mysqlHost,
            'port'      => $mysqlPort,
            'database'  => $mysqlDatabase,
            'username'  => $mysqlUsername,
            'password'  => $mysqlPassword,
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
            'strict'    => true,
            'engine'    => null,
        ];

        Config::set('database.connections.' . QueryExecutor::CONNECTION_FROM_MYSQL_DATABASE, $fromMysqlDatabaseConfig);

        DB::purge(QueryExecutor::CONNECTION_FROM_MYSQL_DATABASE);

        Log::info('Truncating tables...');

        DB::connection(QueryExecutor::CONNECTION_FROM_MYSQL_DATABASE)->table('customers')->truncate();
        DB::connection(QueryExecutor::CONNECTION_FROM_MYSQL_DATABASE)->table('product_categories')->truncate();
        DB::connection(QueryExecutor::CONNECTION_FROM_MYSQL_DATABASE)->table('products')->truncate();
        DB::connection(QueryExecutor::CONNECTION_FROM_MYSQL_DATABASE)->table('sales')->truncate();
        DB::connection(QueryExecutor::CONNECTION_FROM_MYSQL_DATABASE)->table('sale_products')->truncate();

        $customersQuantity = rand(intval(self::SALES_QUANTITY / 40), intval(self::SALES_QUANTITY / 20));
        Log::info('Inserting Customers: ' . $customersQuantity);

        for ($i = 0; $i < $customersQuantity; $i++) {
            $faker = Faker::create();

            $customer = [
                'name' => $faker->name(),
                'group' => $this->groups[rand(0, 3)]
            ];

            $id = DB::connection(QueryExecutor::CONNECTION_FROM_MYSQL_DATABASE)->table('customers')->insertGetId($customer);

            $this->customersIds[] = $id;
        }

        $productsCategoriesQuantity = rand(1, 4);
        Log::info('Inserting Product Categories: ' . $productsCategoriesQuantity);

        for ($i = 0; $i < $productsCategoriesQuantity; $i++) {
            $faker = Faker::create();

            $productCategory = [
                'title' => $faker->name()
            ];

            $id = DB::connection(QueryExecutor::CONNECTION_FROM_MYSQL_DATABASE)->table('product_categories')->insertGetId($productCategory);

            $this->productCategoriesIds[] = $id;
        }

        $productsQuantity = rand(1, 30);
        Log::info('Inserting Products: ' . $productsQuantity);

        for ($i = 0; $i < $productsQuantity; $i++) {
            $faker = Faker::create();

            $product = [
                'name' => $faker->name(),
                'category_id' => $this->productCategoriesIds[rand(0, ($productsCategoriesQuantity - 1))]
            ];

            $id = DB::connection(QueryExecutor::CONNECTION_FROM_MYSQL_DATABASE)->table('products')->insertGetId($product);

            $this->productsIds[] = $id;
        }

        Log::info('Inserting Sales Data: ' . self::SALES_QUANTITY);
        for ($i = 0; $i < self::SALES_QUANTITY; $i++) {
            Log::info('Inserting Sales Data (' . $i . '): ');
            $faker = Faker::create();

            $sale = [
                'customer_id' => $this->customersIds[rand(0, intval($customersQuantity - 1))],
                'date' => $faker->date()
            ];

            $saleId = DB::connection(QueryExecutor::CONNECTION_FROM_MYSQL_DATABASE)->table('sales')->insertGetId($sale);

            $saleProductQuantity = rand(1, $productsQuantity);

            $usedProductIds = [];

            for ($k = 0; $k < $saleProductQuantity; $k++) {
                $faker = Faker::create();

                $productId = null;

                do {
                    $productId = $this->productsIds[rand(0, intval($productsQuantity -1))];
                } while(in_array($productId, $usedProductIds));

                $usedProductIds[] = $productId;

                $saleProduct = [
                    'sale_id'    => $saleId,
                    'product_id' => $productId,
                    'quantity'   => rand(1, 50),
                    'unit_value' => round(floatval(rand(10, 100) / 10), 2)
                ];

                DB::connection(QueryExecutor::CONNECTION_FROM_MYSQL_DATABASE)->table('sale_products')->insert($saleProduct);
            }
        }
    }
}
