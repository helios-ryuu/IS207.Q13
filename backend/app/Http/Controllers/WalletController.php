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
        $userId = Auth::id();
        $wallet = $this->walletService->getWallet($userId);
        
        return response()->json([
            'status' => 'success', 
            'data' => new WalletResource($wallet)
        ]);
    }

    // 2. POST /api/wallet/deposit - Nạp tiền
    public function deposit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000', // Nạp tối thiểu 10k
        ]);

        try {
            $userId = Auth::id();
            $wallet = $this->walletService->deposit($userId, $request->amount);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Nạp tiền thành công!',
                'current_balance' => number_format($wallet->balance) . ' VNĐ'
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    // 3. POST /api/wallet/withdraw - Rút tiền
    public function withdraw(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000',
        ]);

        try {
            $userId = Auth::id();
            $wallet = $this->walletService->withdraw($userId, $request->amount);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Rút tiền thành công!',
                'current_balance' => number_format($wallet->balance) . ' VNĐ'
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    // 4. GET /api/wallet/transactions - Lịch sử giao dịch (Của các đơn hàng)
    public function history()
    {
        $userId = Auth::id();
        $transactions = $this->walletService->getHistory($userId);
        
        return response()->json([
            'status' => 'success', 
            'data' => TransactionResource::collection($transactions)
        ]);
    }
}