<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     * Thêm status 'pending', 'rejected', 'hidden' vào bảng products
     */
    public function up(): void
    {
        // MySQL: Sử dụng raw SQL để thay đổi enum
        DB::statement("ALTER TABLE products MODIFY COLUMN status ENUM('active', 'inactive', 'out_of_stock', 'pending', 'rejected', 'hidden') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback: Trở về enum cũ
        // Lưu ý: Nếu có dữ liệu với status mới, cần xử lý trước khi rollback
        DB::statement("UPDATE products SET status = 'active' WHERE status IN ('pending', 'rejected', 'hidden')");
        DB::statement("ALTER TABLE products MODIFY COLUMN status ENUM('active', 'inactive', 'out_of_stock') DEFAULT 'active'");
    }
};
