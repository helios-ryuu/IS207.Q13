<?php

namespace App\Http\Controllers;

use App\Services\TransactionService;
use App\Http\Resources\TransactionResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class TransactionController extends Controller
{
    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    // 1. GET /api/transactions - Danh sách giao dịch
    public function index()
    {
        $userId = Auth::id();
        $transactions = $this->transactionService->getUserTransactions($userId);
        return response()->json(['status' => 'success', 'data' => TransactionResource::collection($transactions)]);
    }

    // 2. GET /api/transactions/{id} - Chi tiết giao dịch
    public function show($id)
    {
        try {
            $userId = Auth::id();
            $transaction = $this->transactionService->getTransactionDetail($userId, $id);
            return response()->json(['status' => 'success', 'data' => new TransactionResource($transaction)]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 404);
        }
    }

    // 3. POST /api/transactions/verify - Xác thực thanh toán
    public function verify(Request $request)
    {
        // Validate dữ liệu đầu vào
        $request->validate([
            'transaction_code' => 'required|string',
            'status' => 'required|in:success,failed' // Chỉ chấp nhận 'success' hoặc 'failed'
        ]);

        try {
            // API này thường dùng cho Webhook nên có thể không cần Auth, 
            // nhưng để an toàn trong lúc test cứ để Auth hoặc middleware riêng.
            $transaction = $this->transactionService->verifyTransaction($request->transaction_code, $request->status);
            
            return response()->json([
                'status' => 'success', 
                'message' => 'Xác thực thanh toán thành công!', 
                'data' => new TransactionResource($transaction)
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }
}