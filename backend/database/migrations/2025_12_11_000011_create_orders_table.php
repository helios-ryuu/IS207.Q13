<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->dateTime('order_date')->useCurrent();
            $table->dateTime('delivery_date')->nullable();
            $table->decimal('shipping_fee', 15, 2)->default(0.00);
            $table->decimal('total_amount', 15, 2)->default(0.00); // [FIX] Tổng tiền đơn hàng
            $table->enum('status', ['pending', 'confirmed', 'processing', 'shipping', 'shipped', 'delivered', 'completed', 'cancelled', 'return', 'refunded'])->default('pending');
            $table->text('notes')->nullable();
            $table->enum('payment_method', ['cash', 'bank_transfer', 'wallet', 'credit_card']);
            $table->string('tracking_code', 100)->nullable()->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('address_id')->constrained('shipping_addresses')->onDelete('restrict');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
