<?php

namespace App\Http\Controllers;

use App\Services\AddressService;
use App\Http\Resources\AddressResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class ShippingAddressController extends Controller
{
    protected $addressService;

    public function __construct(AddressService $addressService)
    {
        $this->addressService = $addressService;
    }

    // GET /api/addresses
    public function index()
    {
        $userId = Auth::id();
        $addresses = $this->addressService->getAddresses($userId);
        return response()->json(['status' => 'success', 'data' => AddressResource::collection($addresses)]);
    }

    // POST /api/addresses
    public function store(Request $request)
    {
        $request->validate([
            'receiver_name' => 'required|string',
            'phone_number' => 'required|string',
            'street_address' => 'required|string',
            'ward' => 'required|string',
            'district' => 'required|string',
            'province' => 'required|string',
        ]);

        try {
            $userId = Auth::id();
            $this->addressService->createAddress($userId, $request->all());
            return response()->json(['status' => 'success', 'message' => 'Thêm địa chỉ thành công'], 201);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    // PUT /api/addresses/{id}
    public function update(Request $request, $id)
    {
        try {
            $userId = Auth::id();
            $this->addressService->updateAddress($userId, $id, $request->all());
            return response()->json(['status' => 'success', 'message' => 'Cập nhật thành công']);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    // DELETE /api/addresses/{id}
    public function destroy($id)
    {
        try {
            $userId = Auth::id();
            $this->addressService->deleteAddress($userId, $id);
            return response()->json(['status' => 'success', 'message' => 'Đã xóa địa chỉ']);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    // PUT /api/addresses/{id}/set-default
    public function setDefault($id)
    {
        try {
            $userId = Auth::id();
            $this->addressService->setDefault($userId, $id);
            return response()->json(['status' => 'success', 'message' => 'Đã đặt làm địa chỉ mặc định']);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }
}