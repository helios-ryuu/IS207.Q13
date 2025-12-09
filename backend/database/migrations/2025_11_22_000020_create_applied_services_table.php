<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applied_services', function (Blueprint $table) {
            $table->foreignId('post_id')->constrained('product_posts')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('services')->onDelete('restrict');
            $table->timestamps();
            
            $table->primary(['post_id', 'service_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applied_services');
    }
};
