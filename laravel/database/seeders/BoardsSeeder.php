<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BoardsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info("Create game boards");
        DB::table('boards')->insert([
            [
                'board_cols' => 3,
                'board_rows' => 4,
                'description' => 'A beginner-friendly board with 12 cards, perfect for quick and casual gameplay.',
                'price' => null,
                'guest_enable' => 1,
            ],
            [
                'board_cols' => 4,
                'board_rows' => 4,
                'description' => 'A balanced 16-card challenge, offering the right mix of fun and strategy for all players.',
                'price' => 1,
                'guest_enable' => 0,
            ],
            [
                'board_cols' => 6,
                'board_rows' => 6,
                'description' => 'The ultimate 36-card test for memory masters, delivering intense and engaging gameplay for experienced players.',
                'price' => 1,
                'guest_enable' => 0,
            ]
        ]);
    }
}
