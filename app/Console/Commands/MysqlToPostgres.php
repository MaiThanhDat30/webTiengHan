<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MysqlToPostgres extends Command
{
    protected $signature = 'mysql-to-postgres';

    protected $description = 'Migrate data from MySQL to PostgreSQL';

    public function handle()
    {
        $mysql = DB::connection('mysql');
        $pg    = DB::connection('pgsql_render');

        // ⚠️ THỨ TỰ CHA → CON (SỬA NẾU DB BẠN CÓ THÊM BẢNG)
        $tables = [
            'users',
            'topics',          // 👈 PHẢI CÓ TRƯỚC
            'vocabularies',
            'learning_logs',
        ];

        foreach ($tables as $tableName) {
            $this->info("Migrating table: {$tableName}");

            if (!$mysql->getSchemaBuilder()->hasTable($tableName)) {
                $this->warn("Table {$tableName} not found in MySQL");
                continue;
            }

            $mysql->table($tableName)
                ->orderBy('id')
                ->chunk(500, function ($rows) use ($pg, $tableName) {
                    foreach ($rows as $row) {
                        $pg->table($tableName)->insert((array) $row);
                    }
                });

            // 🔁 Reset sequence cho PostgreSQL
            $pg->statement("
                SELECT setval(
                    pg_get_serial_sequence('{$tableName}', 'id'),
                    (SELECT MAX(id) FROM {$tableName})
                )
            ");
        }

        $this->info('🎉 DONE: MySQL → PostgreSQL');
    }
}
