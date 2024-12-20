<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterPersonalStatisticsRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use function Illuminate\Events\queueable;

class StatisticsController extends Controller
{
    public function personalStatistics(FilterPersonalStatisticsRequest $request)
    {
        $startDate = $request->validated('start_date') ?? now()->subYear();
        $endDate = $request->validated('end_date') ?? now();
        $userId = auth()->id(); // Current logged-in user

        // === SINGLE PLAYER STATISTICS ===
        $singlePlayerData = DB::table('games')
            ->join('boards', 'games.board_id', '=', 'boards.id')
            ->where('games.created_user_id', $userId)
            ->where('games.type', 'S') // Single-player games
            ->whereBetween('games.began_at', [$startDate, $endDate])
            ->whereNotNull('games.ended_at')
            ->selectRaw("
               CONCAT(boards.board_cols, 'x', boards.board_rows) as board_size,
                COUNT(*) as total_games,
                AVG(TIMESTAMPDIFF(SECOND, games.began_at, games.ended_at)) as avg_time,
                SUM(TIMESTAMPDIFF(SECOND, games.began_at, games.ended_at)) as total_time,
                MAX(TIMESTAMPDIFF(SECOND, games.began_at, games.ended_at)) as max_time,
                MIN(TIMESTAMPDIFF(SECOND, games.began_at, games.ended_at)) as min_time,
                AVG(games.total_turns_winner) as avg_turns,
                MAX(games.total_turns_winner) as max_turns,
                MIN(games.total_turns_winner) as min_turns")
            ->groupBy('boards.board_cols', 'boards.board_rows')
            ->get();

        $totalTimePlayed = DB::table('games')
            ->where('games.created_user_id', $userId)
            ->where('games.type', 'S') // Single-player games
            ->whereBetween('games.began_at', [$startDate, $endDate])
            ->whereNotNull('games.ended_at')
            ->sum(DB::raw("TIMESTAMPDIFF(SECOND, games.began_at, games.ended_at)"));

        $singlePlayer = [
            'totalGames' => $singlePlayerData->sum('total_games'),
            'totalTimePlayed' => $totalTimePlayed,
            'boards' => $singlePlayerData->mapWithKeys(function ($data) {
                return [
                    $data->board_size => [
                        'totalGames' => $data->total_games,
                        'averageTime' => round($data->avg_time, 2),
                        'totalTimePlayed' => $data->total_time,
                        'minTime' => $data->min_time,
                        'maxTime' => $data->max_time,
                        'averageTurns' => round($data->avg_turns, 0),
                        'maxTurns' => $data->max_turns,
                        'minTurns' => $data->min_turns,
                    ]
                ];
            })
        ];

        // === MULTIPLAYER STATISTICS ===
        $multiplayerGlobalData = DB::table('multiplayer_games_played')
            ->join('games', 'multiplayer_games_played.game_id', '=', 'games.id')
            ->where('multiplayer_games_played.user_id', $userId)
            ->whereBetween('games.began_at', [$startDate, $endDate])
            ->whereNotNull('games.ended_at')
            ->selectRaw("
        COUNT(*) as total_games,
        SUM(CASE WHEN games.winner_user_id = ? THEN 1 ELSE 0 END) as wins,
        SUM(CASE WHEN games.winner_user_id != ? AND games.winner_user_id IS NOT NULL THEN 1 ELSE 0 END) as loses,
        SUM(multiplayer_games_played.pairs_discovered) as total_pairs_discovered", [$userId, $userId])
            ->first();

        $multiplayerByBoard = DB::table('multiplayer_games_played')
            ->join('games', 'multiplayer_games_played.game_id', '=', 'games.id')
            ->join('boards', 'games.board_id', '=', 'boards.id')
            ->where('multiplayer_games_played.user_id', $userId)
            ->whereBetween('games.began_at', [$startDate, $endDate])
            ->whereNotNull('games.ended_at')
            ->selectRaw("
CONCAT(boards.board_cols, 'x', boards.board_rows) as board_size,
COUNT(*) as total_games,
AVG(multiplayer_games_played.pairs_discovered) as avg_pairs_discovered,
SUM(multiplayer_games_played.pairs_discovered) as total_pairs_discovered,
SUM(CASE WHEN games.winner_user_id = ? THEN 1 ELSE 0 END) as board_wins,
SUM(CASE WHEN games.winner_user_id != ? AND games.winner_user_id IS NOT NULL THEN 1 ELSE 0 END) as borad_loses", [$userId, $userId])
            ->groupBy('boards.board_cols', 'boards.board_rows')
            ->orderBy('boards.board_cols')
            ->orderBy('boards.board_rows')
            ->get();

        $multiPlayer = [
            'totalGames' => $multiplayerGlobalData->total_games ?? 0,
            'wins' => $multiplayerGlobalData->wins ?? 0,
            'loses' => $multiplayerGlobalData->loses ?? 0,
            'winRate' => $multiplayerGlobalData->total_games > 0
                ? round(($multiplayerGlobalData->wins / $multiplayerGlobalData->total_games) * 100, 2) . '%'
                : '0%',
            'totalPairsDiscovered' => $multiplayerGlobalData->total_pairs_discovered ?? 0,
            'boards' => $multiplayerByBoard->map(function ($data) {
                return [
                    'totalGames' => $data->total_games,
                    'boardWins' => $data->board_wins,
                    'boardLoses' => $data->borad_loses,
                    'boardSize' => $data->board_size,
                    'averagePairsDiscovered' => round($data->avg_pairs_discovered, 2),
                    'totalPairsDiscovered' => $data->total_pairs_discovered,
                ];
            })->toArray(),
        ];

        // === PURCHASES STATISTICS ===
        $purchaseData = DB::table('transactions')
            ->where('user_id', $userId)
            ->where('type', 'P') // Purchase transactions
            ->whereBetween('transaction_datetime', [$startDate, $endDate])
            ->selectRaw("
                COUNT(*) as total_purchases,
                AVG(euros) as avg_purchase,
                SUM(euros) as total_spent
            ")
            ->first();

        $mostFrequentPurchase = DB::table('transactions')
            ->where('user_id', $userId)
            ->where('type', 'P')
            ->whereBetween('transaction_datetime', [$startDate, $endDate])
            ->selectRaw('euros, COUNT(*) as count')
            ->groupBy('euros')
            ->orderByDesc('count')
            ->orderBy('euros')
            ->first();

        $purchases = [
            'totalPurchases' => $purchaseData->total_purchases ?? 0,
            'averagePurchase' => $mostFrequentPurchase->euros ?? 0,
            'totalSpent' => round($purchaseData->total_spent ?? 0, 2),
            'averageSpent' => ($purchaseData->total_purchases ?? 0) > 0
                ? round(($purchaseData->total_spent / $purchaseData->total_purchases), 2)
                : 0,

        ];

        // === RESPONSE ===
        return response()->json([
            'games' => [
                'singlePlayer' => $singlePlayer,
                'multiPlayer' => $multiPlayer,
            ],
            'purchases' => $purchases,
        ]);
    }

    public function globalStatistics()
    {
        $totalPlayers = DB::table('users')
            ->where('type', 'P')
            ->count();

        $totalGamesPlayed = DB::table('games')
            ->count();

        $gamesPlayedLastWeek = DB::table('games')
            ->where('created_at', '>=', now()->subWeek())
            ->count();

        $gamesPlayedLastMonth = DB::table('games')
            ->where('created_at', '>=', now()->subMonth())
            ->count();

        $totalSinglePlayerGames = DB::table('games')
            ->where('type', 'S')
            ->count();

        $totalMultiplayerGames = DB::table('games')
            ->where('type', 'M')
            ->count();

        return response()->json([
            'totalPlayers' => $totalPlayers,
            'totalGamesPlayed' => $totalGamesPlayed,
            'gamesPlayedLastWeek' => $gamesPlayedLastWeek,
            'gamesPlayedLastMonth' => $gamesPlayedLastMonth,
            'totalSinglePlayerGames' => $totalSinglePlayerGames,
            'totalMultiplayerGames' => $totalMultiplayerGames,
        ]);

    }

    public
    function adminStatistics()
    {
        $purchasesByDate = DB::table('transactions')
            ->where('type', 'P')
            ->select(
                DB::raw("DATE_FORMAT(transaction_datetime, '%Y-%m-%d') as date"),
                DB::raw('SUM(euros) as total')
            )
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();


        $purchasesByPlayer = DB::table('transactions')
            ->where('transactions.type', 'P')
            ->join('users', 'transactions.user_id', '=', 'users.id')
            ->select('users.nickname', DB::raw('SUM(transactions.euros) as total'))
            ->groupBy('users.nickname')
            ->orderBy('total', 'desc')
            ->get();


        $gamesByDay = DB::table('games')
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total_games')
            ->groupBy('day')
            ->orderBy('day', 'desc')
            ->get();

        $transactionsByDay = DB::table('transactions')
            ->selectRaw('DATE(transaction_datetime) as day, SUM(euros) as total_amount')
            ->groupBy('day')
            ->orderBy('day', 'desc')
            ->get();

        $topPlayers = DB::table('multiplayer_games_played')
            ->join('users', 'multiplayer_games_played.user_id', '=', 'users.id')
            ->where('player_won', true)
            ->select('users.nickname', DB::raw('COUNT(*) as total_wins'))
            ->groupBy('users.nickname')
            ->orderBy('total_wins', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'purchasesByDate' => $purchasesByDate,
            'purchasesByPlayer' => $purchasesByPlayer,
            'topPlayers' => $topPlayers,
            'gamesByDay' => $gamesByDay,
            'transactionsByDay' => $transactionsByDay
        ]);
    }
}
