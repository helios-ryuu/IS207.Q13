<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            // Liên kết khóa ngoại
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('rating')->unsigned();
            $table->text('content')->nullable();
            $table->dateTime('review_date')->useCurrent();
            $table->timestamps();

            // $table->unique(['product_id', 'user_id']); // Bỏ unique để cho phép mua nhiều lần đánh giá nhiều lần
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
