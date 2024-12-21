<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionStoreRequest;
use App\Http\Requests\FilterTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Models\User;

class TransactionController extends Controller
{
    public function index(FilterTransactionRequest $request)
    {
        $query = Transaction::query()->with('user');

        if ($request->user()->type == 'P') {
            $query->where('user_id', $request->user()->id);
        }

        if ($request->has('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('nickname', 'like', '%' . $request->search . '%');
            });

        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Sorting
        $sortField = $request->input('sort_by', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');

        $allowedSortFields = [
            'id',
            'transaction_datetime',
            'brain_coins',
        ];

        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection === 'asc' ? 'asc' : 'desc');
        }

        // Pagination
        $perPage = $request->input('per_page', 20);
        $transactions = $query->paginate($perPage);

        return TransactionResource::collection($transactions);
    }

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

        if ($request->has('description')) {
            $transaction->custom = json_encode(['description' => $request->description]);
        }

        //Update brain_coins_balance
        $user->brain_coins_balance += $request->validated("brain_coins");
        $user->save();

        $transaction->save();

        return new TransactionResource($transaction);
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
