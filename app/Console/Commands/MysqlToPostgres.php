<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MysqlToPostgres extends Command
{
    protected $signature = 'mysql-to-postgres';

    protected $description = 'Sync data from MySQL to PostgreSQL (safe re-run)';

    public function handle()
    {
        $mysql = DB::connection('mysql');
        $pg    = DB::connection('pgsql_render');

        // ⚠️ THỨ TỰ CHA → CON
        $tables = [
            'users',
            'topics',
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
                        $data = (array) $row;

                        // 🛑 FIX RIÊNG CHO vocabularies
                        if ($tableName === 'vocabularies') {
                            $allowedCategories = [1, 2, 3, 4, 5];
                            if (!in_array($data['category'], $allowedCategories)) {
                                $data['category'] = 1;
                            }
                        }

                        try {
                            // ✅ UPSERT – chạy lại không trùng
                            $pg->table($tableName)->updateOrInsert(
                                ['id' => $data['id']], // khóa chính
                                $data
                            );
                        } catch (\Throwable $e) {
                            $this->error(
                                "❌ Sync fail table={$tableName} id={$data['id']} : "
                                . $e->getMessage()
                            );
                        }
                    }
                });

            // 🔁 Fix sequence cho PostgreSQL
            $pg->statement("
                SELECT setval(
                    pg_get_serial_sequence('{$tableName}', 'id'),
                    COALESCE((SELECT MAX(id) FROM {$tableName}), 1)
                )
            ");
        }

        $this->info('🎉 DONE: MySQL → PostgreSQL (SAFE)');
    }
}
