<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    // Chỉ seller sở hữu sản phẩm mới được update/delete
    public function update(User $user, Product $product): bool
    {
        return $user->id === $product->seller_id;
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->id === $product->seller_id;
    }
    
    // Chỉ seller mới được tạo
    public function create(User $user): bool
    {
        return $user->role === 'seller' || $user->role === 'admin';
    }
}