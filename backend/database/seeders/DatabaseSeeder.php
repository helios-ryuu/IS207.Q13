<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
                // 1. Base data (không phụ thuộc)
            CategorySeeder::class,
            UserSeeder::class,
            ServiceSeeder::class,
            PromotionSeeder::class,

                // 2. User-dependent data
            WalletSeeder::class,
            ShippingAddressSeeder::class,
            BankAccountSeeder::class,

                // 3. Product data
            ProductSeeder::class,
            ProductPostSeeder::class,

                // 4. Business data (phụ thuộc product + user)
            FavoriteSeeder::class,
            CartSeeder::class,
            OrderSeeder::class,
            ReviewSeeder::class,
            MessageSeeder::class,
            NotificationSeeder::class,

                // 5. Junction tables (phụ thuộc orders + posts)
            AppliedPromotionSeeder::class,
            AppliedServiceSeeder::class,
        ]);
    }
}