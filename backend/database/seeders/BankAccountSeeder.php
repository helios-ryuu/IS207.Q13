<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BankAccountSeeder extends Seeder
{
    public function run(): void
    {
        $sellers = User::where('role', 'seller')->get();

        $banks = [
            ['Vietcombank', 'VCB'],
            ['Techcombank', 'TCB'],
            ['BIDV', 'BIDV'],
            ['VietinBank', 'CTG'],
            ['MB Bank', 'MBB'],
            ['ACB', 'ACB'],
            ['Sacombank', 'STB'],
            ['TPBank', 'TPB'],
        ];

        $branches = [
            'Chi nhánh Quận 1',
            'Chi nhánh Thủ Đức',
            'Chi nhánh Tân Bình',
            'Chi nhánh Gò Vấp',
            'Chi nhánh Bình Thạnh',
            'Chi nhánh Hà Nội',
            'Chi nhánh Đà Nẵng',
            'Chi nhánh Cần Thơ',
        ];

        $accountCount = 0;

        foreach ($sellers as $seller) {
            // Each seller gets 1-3 bank accounts
            $numAccounts = rand(1, 3);

            for ($i = 0; $i < $numAccounts && $accountCount < 15; $i++) {
                $bank = $banks[array_rand($banks)];
                $accountNumber = $bank[1] . rand(1000000000, 9999999999);

                $exists = DB::table('bank_accounts')
                    ->where('account_number', $accountNumber)
                    ->exists();

                if ($exists)
                    continue;

                DB::table('bank_accounts')->insert([
                    'user_id' => $seller->id,
                    'account_holder_name' => strtoupper($seller->full_name),
                    'account_number' => $accountNumber,
                    'bank_name' => $bank[0],
                    'branch' => $branches[array_rand($branches)],
                    'status' => $i === 0 ? 'active' : (rand(0, 1) ? 'active' : 'inactive'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $accountCount++;
            }
        }
    }
}
