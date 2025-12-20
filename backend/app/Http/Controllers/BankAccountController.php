<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BankAccountController extends Controller
{
    // GET /api/user/bank-accounts - Danh sách tài khoản ngân hàng
    public function index()
    {
        $accounts = BankAccount::where('user_id', Auth::id())
            ->orderBy('status', 'desc') // active first
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $accounts->map(fn($acc) => [
                'id' => $acc->id,
                'account_holder_name' => $acc->account_holder_name,
                'account_number' => $acc->account_number,
                'account_number_masked' => $this->maskAccountNumber($acc->account_number),
                'bank_name' => $acc->bank_name,
                'branch' => $acc->branch,
                'status' => $acc->status,
                'is_default' => $acc->status === 'active',
            ])
        ]);
    }

    // POST /api/user/bank-accounts - Thêm tài khoản mới
    public function store(Request $request)
    {
        $request->validate([
            'account_holder_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:50',
            'bank_name' => 'required|string|max:100',
            'branch' => 'nullable|string|max:255',
        ]);

        // Kiểm tra trùng
        $exists = BankAccount::where('user_id', Auth::id())
            ->where('account_number', $request->account_number)
            ->where('bank_name', $request->bank_name)
            ->exists();

        if ($exists) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tài khoản ngân hàng này đã được liên kết!'
            ], 422);
        }

        // Nếu đây là tài khoản đầu tiên, set làm mặc định
        $isFirst = !BankAccount::where('user_id', Auth::id())->exists();

        $account = BankAccount::create([
            'user_id' => Auth::id(),
            'account_holder_name' => strtoupper($request->account_holder_name),
            'account_number' => $request->account_number,
            'bank_name' => $request->bank_name,
            'branch' => $request->branch ?? '',
            'status' => $isFirst ? 'active' : 'inactive',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Thêm tài khoản ngân hàng thành công!',
            'data' => [
                'id' => $account->id,
                'account_holder_name' => $account->account_holder_name,
                'account_number_masked' => $this->maskAccountNumber($account->account_number),
                'bank_name' => $account->bank_name,
                'status' => $account->status,
            ]
        ], 201);
    }

    // PUT /api/user/bank-accounts/{id}/set-default - Đặt làm mặc định
    public function setDefault($id)
    {
        $account = BankAccount::where('user_id', Auth::id())->findOrFail($id);

        // Bỏ active tất cả tài khoản khác
        BankAccount::where('user_id', Auth::id())
            ->where('id', '!=', $id)
            ->update(['status' => 'inactive']);

        // Set active tài khoản này
        $account->status = 'active';
        $account->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Đã đặt làm tài khoản mặc định'
        ]);
    }

    // DELETE /api/user/bank-accounts/{id} - Xóa tài khoản
    public function destroy($id)
    {
        $account = BankAccount::where('user_id', Auth::id())->findOrFail($id);

        $wasActive = $account->status === 'active';
        $account->delete();

        // Nếu xóa tài khoản active, set tài khoản khác làm active
        if ($wasActive) {
            $nextAccount = BankAccount::where('user_id', Auth::id())->first();
            if ($nextAccount) {
                $nextAccount->status = 'active';
                $nextAccount->save();
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Đã xóa tài khoản ngân hàng'
        ]);
    }

    // Helper: Mask số tài khoản
    private function maskAccountNumber($number)
    {
        $len = strlen($number);
        if ($len <= 4)
            return $number;
        return str_repeat('*', $len - 4) . substr($number, -4);
    }
}
