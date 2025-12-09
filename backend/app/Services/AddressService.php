<?php

namespace App\Services;

use App\Models\ShippingAddress;
use Exception;
use Illuminate\Support\Facades\DB;

class AddressService
{
    // Lấy danh sách địa chỉ của User
    public function getAddresses($userId)
    {
        // Sắp xếp để địa chỉ mặc định lên đầu tiên
        return ShippingAddress::where('user_id', $userId)
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    // Thêm địa chỉ mới
    public function createAddress($userId, $data)
    {
        // Nếu user chưa có địa chỉ nào, cái đầu tiên auto là mặc định
        $count = ShippingAddress::where('user_id', $userId)->count();
        if ($count == 0) {
            $data['is_default'] = true;
        }

        $data['user_id'] = $userId;
        return ShippingAddress::create($data);
    }

    // Cập nhật địa chỉ
    public function updateAddress($userId, $id, $data)
    {
        $address = ShippingAddress::where('user_id', $userId)->where('id', $id)->first();
        if (!$address) {
            throw new Exception("Địa chỉ không tồn tại.");
        }
        $address->update($data);
        return $address;
    }

    // Xóa địa chỉ
    public function deleteAddress($userId, $id)
    {
        $address = ShippingAddress::where('user_id', $userId)->where('id', $id)->first();
        if (!$address) {
            throw new Exception("Địa chỉ không tồn tại.");
        }
        return $address->delete();
    }

    // Đặt làm mặc định (Logic khó nhất phần này)
    public function setDefault($userId, $id)
    {
        // Dùng Transaction để đảm bảo dữ liệu an toàn
        return DB::transaction(function () use ($userId, $id) {
            // 1. Tìm địa chỉ cần set
            $address = ShippingAddress::where('user_id', $userId)->where('id', $id)->first();
            if (!$address) {
                throw new Exception("Địa chỉ không tồn tại.");
            }

            // 2. Bỏ mặc định của tất cả địa chỉ khác của user này
            ShippingAddress::where('user_id', $userId)->update(['is_default' => false]);

            // 3. Set địa chỉ này thành mặc định
            $address->is_default = true;
            $address->save();

            return $address;
        });
    }
}