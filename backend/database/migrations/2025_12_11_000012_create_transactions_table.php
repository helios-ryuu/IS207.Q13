<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            
            // [MỚI] Thêm user_id để biết giao dịch Nạp/Rút là của ai
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // [SỬA] Thêm ->nullable() vì Nạp/Rút tiền không gắn với đơn hàng nào cả
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('restrict');
            
            $table->decimal('amount', 15, 2);
            $table->enum('payment_method', ['cash', 'bank_transfer', 'wallet', 'credit_card']);
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->string('transaction_code', 100)->nullable()->unique();
            $table->string('payment_gateway', 50)->nullable();
            $table->dateTime('transaction_date')->useCurrent();
            $table->text('response_data')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};