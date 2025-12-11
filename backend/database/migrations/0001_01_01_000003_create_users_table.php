<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 50)->unique();
            $table->string('email', 100)->unique();
            $table->string('password');
            $table->string('full_name', 100);
            $table->string('phone_number', 30)->unique()->nullable();
            $table->text('address')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('avatar')->nullable();
            $table->string('cover')->nullable();
            $table->text('bio')->nullable();
            $table->string('website')->nullable();
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('twitter')->nullable();
            $table->enum('status', ['active', 'inactive', 'banned', 'suspended'])->default('active');
            $table->enum('role', ['customer', 'seller', 'admin'])->default('customer');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
