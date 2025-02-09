<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            BuyerSeeder::class,
            SellerSeeder::class,
            ProductSeeder::class,
            DescriptionSeeder::class,
            CategorySeeder::class,
            CartSeeder::class,
            AddressSeeder::class,
            TransactionHeaderSeeder::class,
            TransactionDetailSeeder::class,
            ArticleSeeder::class,
            ProductCategorySeeder::class
        ]);
    }
}
