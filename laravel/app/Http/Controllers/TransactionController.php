<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionStoreRequest;
use App\Http\Requests\FilterTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Models\User;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(FilterTransactionRequest $request)
    {
        return $this->show($request, -1);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TransactionStoreRequest $request)
    {
        $transaction = new Transaction();
        $user = $request->user();

        $transaction->transaction_datetime = now();
        $transaction->user_id = $user->id;
        $transaction->fill($request->validated());

        switch ($request->type) {
            case "I":
                $transaction->game_id = $request->validated("game_id");
                break;
            case "P":
                $transaction->euros = $request->validated("euros");
                $transaction->payment_type = $request->validated("payment_type");
                $transaction->payment_reference = $request->validated("payment_reference");
                break;
        }

        if($request->has('description')) {
            $transaction->custom = json_encode(['description' => $request->description]);
        }

        //Update brain_coins_balance
        $user->brain_coins_balance += $request->validated("brain_coins");
        $user->save();

        $transaction->save();

        return new TransactionResource($transaction);
    }

    /**
     * Display the specified resource.
     */
    public function show(FilterTransactionRequest $request, int $id)
    {
        $transactionsQuery = Transaction::query();

        if ($id !== -1) {
            $transactionsQuery->where('user_id', $id);
        }

        $filterByType = $request->validated('type');
        if ($filterByType !== null) {
            $transactionsQuery->where('type', $filterByType);
        }

        $orderBy = $request->validated('order_by');
        if ($orderBy !== null) {
            switch ($orderBy) {
                case 'date':
                    $transactionsQuery->orderBy('transaction_datetime', 'desc');
                    break;
                case 'coins':
                    $transactionsQuery->orderBy('brain_coins', 'desc');
                    break;
            }
        }

        $transactions = $transactionsQuery
            ->paginate(20)
            ->withQueryString();

        return TransactionResource::collection($transactions);
    }

    /**
     * Display the transactions of the authenticated user.
     */
    public function user_transactions(FilterTransactionRequest $request)
    {
        return $this->show($request, $request->user()->id);
    }

    public function destroy(Transaction $transaction)
    {
        $user = User::find($transaction->user_id);
        $user->brain_coins_balance -= $transaction->brain_coins;
        $user->save();
        $transaction->delete();
        return response()->json(['message' => 'Transaction deleted'], 200);
    }
}
