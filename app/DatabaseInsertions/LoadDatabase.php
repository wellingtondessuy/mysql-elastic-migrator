<?php


namespace App\DatabaseInsertions;

use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Log;

class LoadDatabase
{
    const SALES_QUANTITY = 10000;

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

        $customersQuantity = rand(intval(self::SALES_QUANTITY / 10), intval(self::SALES_QUANTITY / 4));
        Log::info('Inserting Customers: ' . $customersQuantity);

        for ($i = 0; $i < $customersQuantity; $i++) {
            $faker = Faker::create();

            $customer = [
                'name' => $faker->name(),
                'group' => $this->groups[rand(0, 3)]
            ];

            $id = DB::table('customers')->insertGetId($customer);

            $this->customersIds[] = $id;
        }

        $productsCategoriesQuantity = rand(1, 10);
        Log::info('Inserting Product Categories: ' . $productsCategoriesQuantity);

        for ($i = 0; $i < $productsCategoriesQuantity; $i++) {
            $faker = Faker::create();

            $productCategory = [
                'title' => $faker->name()
            ];

            $id = DB::table('product_categories')->insertGetId($productCategory);

            $this->productCategoriesIds[] = $id;
        }

        $productsQuantity = rand(1, 100);
        Log::info('Inserting Products: ' . $productsQuantity);

        for ($i = 0; $i < $productsQuantity; $i++) {
            $faker = Faker::create();

            $product = [
                'name' => $faker->name(),
                'category_id' => $this->productCategoriesIds[rand(0, ($productsCategoriesQuantity - 1))]
            ];

            $id = DB::table('products')->insertGetId($product);

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

            $saleId = DB::table('sales')->insertGetId($sale);

            $saleProductQuantity = rand(1, 15);

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

                DB::table('sale_products')->insert($saleProduct);
            }
        }
    }
}
