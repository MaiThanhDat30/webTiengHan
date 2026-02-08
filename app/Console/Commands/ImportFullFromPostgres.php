<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ImportFullFromPostgres extends Command
{
    protected $signature = 'db:import-full';
    protected $description = 'Import FULL database from PostgreSQL (pgsql_old) to MySQL';

    public function handle()
    {
        $this->info('🚀 Starting FULL import from PostgreSQL → MySQL');

        // Lấy toàn bộ table trong PostgreSQL
        $tables = DB::connection('pgsql_old')->select("
            SELECT tablename
            FROM pg_tables
            WHERE schemaname = 'public'
        ");

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        foreach ($tables as $table) {
            $tableName = $table->tablename;
            $this->info("📦 Importing table: {$tableName}");

            // Xoá table cũ nếu tồn tại
            Schema::connection('mysql')->dropIfExists($tableName);

            // Lấy cấu trúc table từ Postgres
            $columns = DB::connection('pgsql_old')->select("
                SELECT column_name, data_type
                FROM information_schema.columns
                WHERE table_name = '{$tableName}'
            ");

            // Tạo table MySQL đơn giản
            Schema::connection('mysql')->create($tableName, function ($table) use ($columns) {

                $hasId = false;

                foreach ($columns as $column) {

                    if ($column->column_name === 'id') {
                        $table->bigIncrements('id'); // 🔥 FIX LỖI FK
                        $hasId = true;
                        continue;
                    }

                    $type = match ($column->data_type) {
                        'integer' => 'integer',
                        'bigint' => 'bigInteger',
                        'boolean' => 'boolean',
                        'timestamp without time zone',
                        'timestamp with time zone' => 'timestamp',
                        'date' => 'date',
                        'text' => 'text',
                        default => 'string',
                    };

                    $table->{$type}($column->column_name)->nullable();
                }

                if (!$hasId) {
                    $table->id();
                }
            });


            // Lấy data
            $rows = DB::connection('pgsql_old')->table($tableName)->get();

            foreach ($rows->chunk(500) as $chunk) {
                DB::connection('mysql')->table($tableName)->insert(
                    json_decode(json_encode($chunk), true)
                );
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->info('✅ IMPORT FULL DATABASE SUCCESS!');
        return Command::SUCCESS;
    }
}
