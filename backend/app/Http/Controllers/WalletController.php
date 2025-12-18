<?php

namespace App\Http\Controllers;

use App\Services\WalletService;
use App\Http\Resources\WalletResource;
use App\Http\Resources\TransactionResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class WalletController extends Controller
{
    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    // 1. GET /api/wallet - Xem số dư
    public function index()
    {
        try {
            $userId = Auth::id();
            $wallet = $this->walletService->getWallet($userId);
            
            return response()->json([
                'status' => 'success', 
                'data' => new WalletResource($wallet)
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // 2. POST /api/wallet/deposit - Nạp tiền
    public function deposit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000',
        ]);

        try {
            $userId = Auth::id();
            $wallet = $this->walletService->deposit($userId, $request->amount);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Nạp tiền thành công!',
                'current_balance' => $wallet->balance // Trả về số trực tiếp hoặc format ở frontend
            ]);
        } catch (Exception $e) {
            // Log lỗi để debug nếu cần
            // \Log::error($e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    // 3. POST /api/wallet/withdraw - Rút tiền
    public function withdraw(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:50000',
        ]);

        try {
            $userId = Auth::id();
            $wallet = $this->walletService->withdraw($userId, $request->amount);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Rút tiền thành công!',
                'current_balance' => $wallet->balance
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    // 4. GET /api/wallet/transactions - Lịch sử
    public function history()
    {
        try {
            $userId = Auth::id();
            $transactions = $this->walletService->getHistory($userId);
            
            return response()->json([
                'status' => 'success', 
                'data' => TransactionResource::collection($transactions)
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}