<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applied_promotions', function (Blueprint $table) {
            $table->foreignId('promotion_id')->constrained('promotions')->onDelete('cascade');
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->decimal('discounted_amount', 15, 2);
            $table->timestamp('created_at')->nullable();
            
            $table->primary(['promotion_id', 'order_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applied_promotions');
    }
};
