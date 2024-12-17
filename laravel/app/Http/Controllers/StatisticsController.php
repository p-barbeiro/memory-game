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
        $start_date = $request->validated('start_date');
        $end_date = $request->validated('end_date');

        if($start_date != null){
            //$query->where...
        }


//        //TODO refactor
////        switch ($id) {
//    case
//        'games':
//                $startDate = $request->query('start_date', now()->subMonth());
//                $endDate = $request->query('end_date', now());
//
//                $totalGames = DB::table('games')
//                    ->whereBetween('created_at', [$startDate, $endDate])
//                    ->count();
//
//                return response()->json([
//                    'message' => 'Game statistics',
//                    'totalGames' => $totalGames,
//                    'period' => ['start' => $startDate, 'end' => $endDate],
//                ]);
//
//            case 'purchases':
//                $totalPurchases = DB::table('transactions')
//                    ->where('type', 'P')
//                    ->sum('euros');
//
//                return response()->json([
//                    'message' => 'Purchase statistics',
//                    'totalPurchases' => $totalPurchases,
//                ]);
//
//            default:
//                return response()->json(['error' => 'Invalid statistics type'], 404);
//        }
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
