<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DbTableStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:db-table-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hiển thị số lượng dòng trong tất cả các bảng, phân loại empty/non-empty';

    public function handle()
    {
        $this->info("\n===== DATABASE TABLE STATUS =====\n");

        $tables = DB::select("SHOW TABLES");
        $dbName = DB::getDatabaseName();

        if (empty($tables)) {
            $this->error("Không tìm thấy bảng nào trong database: $dbName");
            return self::FAILURE;
        }

        $rows = [];

        foreach ($tables as $t) {
            $table = array_values((array)$t)[0];

            try {
                $count = DB::table($table)->count();
            } catch (\Exception $e) {
                $count = null;
            }

            $rows[] = [
                'table' => $table,
                'rows'  => $count,
                'status' => $count === null ? 'ERROR' : ($count > 0 ? 'NON-EMPTY' : 'EMPTY'),
            ];
        }

        // Auto formatting length
        $maxNameLen = max(array_map(fn($r) => strlen($r['table']), $rows));

        foreach ($rows as $r) {
            $name = str_pad($r['table'], $maxNameLen + 2);

            if ($r['status'] === 'ERROR') {
                $this->line("$name => [ERROR: Cannot count]");
                continue;
            }

            if ($r['rows'] == 0) {
                $this->warn("$name => 0 rows  (EMPTY)");
            } else if ($r['rows'] == 1) {
                $this->info("$name => {$r['rows']} row  (NON-EMPTY)");
            } else if ($r['rows'] <= 10) {
                $this->info("$name => {$r['rows']} rows  (NON-EMPTY)");
            } else {
                $this->info("$name => {$r['rows']} rows (NON-EMPTY)");
            }
        }
        return self::SUCCESS;
    }
}

