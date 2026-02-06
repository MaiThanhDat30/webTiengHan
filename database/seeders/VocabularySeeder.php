<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class VocabularySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $familyId = DB::table('topics')->where('slug', 'gia-dinh')->value('id');
        $foodId   = DB::table('topics')->where('slug', 'do-an')->value('id');

        DB::table('vocabularies')->insert([
            [
                'topic_id' => $familyId,
                'word_kr' => '아버지',
                'word_vi' => 'Cha',
                'example' => '아버지는 회사에 가요'
            ],
            [
                'topic_id' => $familyId,
                'word_kr' => '어머니',
                'word_vi' => 'Mẹ',
                'example' => '어머니는 요리를 해요'
            ],
            [
                'topic_id' => $foodId,
                'word_kr' => '밥',
                'word_vi' => 'Cơm',
                'example' => '밥을 먹어요'
            ],
        ]);
    }
}
