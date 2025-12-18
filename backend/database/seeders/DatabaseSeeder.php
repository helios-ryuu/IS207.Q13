<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // 1. Base data (Dữ liệu nền)
            CategorySeeder::class,
            UserSeeder::class,
            ServiceSeeder::class,
            PromotionSeeder::class,

            // 2. User-dependent data (Dữ liệu người dùng)
            WalletSeeder::class,
            ShippingAddressSeeder::class,
            BankAccountSeeder::class,

            // 3. Product data (QUAN TRỌNG: Bỏ comment để tạo sản phẩm)
            ProductSeeder::class,      // <--- ĐÃ BẬT
            ProductPostSeeder::class,  // <--- ĐÃ BẬT (Nếu bạn muốn test cả bài đăng duyệt)

            // 4. Business data (Dữ liệu giao dịch - Bật lên để web sinh động hơn)
            FavoriteSeeder::class,
            CartSeeder::class,
            OrderSeeder::class,
            ReviewSeeder::class,       // <--- Bật review để sản phẩm có đánh giá sao
            MessageSeeder::class,
            NotificationSeeder::class,

            // 5. Junction tables (Nếu cần test khuyến mãi)
            // AppliedPromotionSeeder::class,
            // AppliedServiceSeeder::class,
        ]);
    }
}