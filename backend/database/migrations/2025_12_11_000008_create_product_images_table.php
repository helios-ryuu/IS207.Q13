<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            // --- THÊM DÒNG NÀY ---
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade'); 
            $table->string('image_url', 500);
            $table->foreignId('variant_id')->nullable()->constrained('product_variants')->onDelete('cascade'); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};
