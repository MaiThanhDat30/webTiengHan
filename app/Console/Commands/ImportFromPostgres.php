<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportFromPostgres extends Command
{
    protected $signature = 'import:postgres';
    protected $description = 'Import data from old PostgreSQL to MySQL';

    public function handle()
    {
        $this->info('🚀 Start importing data...');

        DB::connection('mysql')->beginTransaction();

        try {
            $this->importUsers();
            // sau này thêm:
            // $this->importTopics();
            // $this->importVocabularies();

            DB::connection('mysql')->commit();
            $this->info('✅ Import DONE');
        } catch (\Throwable $e) {
            DB::connection('mysql')->rollBack();
            $this->error('❌ Import failed');
            $this->error($e->getMessage());
        }

        return 0;
    }

    private function importUsers()
    {
        $this->info('➡️ Import users');

        $users = DB::connection('pgsql_old')->table('users')->get();

        foreach ($users as $user) {
            DB::connection('mysql')->table('users')->updateOrInsert(
                ['id' => $user->id],
                [
                    'name'       => $user->name,
                    'email'      => $user->email,
                    'password'   => $user->password,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ]
            );
        }

        $this->info('✔ users imported: ' . $users->count());
    }
}
