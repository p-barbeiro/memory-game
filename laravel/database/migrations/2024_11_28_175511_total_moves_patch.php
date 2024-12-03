<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('games', function (Blueprint $table) {
                // Game total moves
                $table->integer('total_turns_winner')->nullable();
            });
        DB::update('update games set total_turns_winner = CASE
                        WHEN board_id = 1 THEN 6 + ROUND(RAND() * 12)
                        WHEN board_id = 2 THEN 8 + ROUND(RAND() * 16)
                        ELSE 18 + ROUND(RAND() * 54)
                    END
                    where total_time is not null');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn('total_turns_winner');
        });
    }
};
