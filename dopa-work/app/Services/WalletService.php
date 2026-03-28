<?php

namespace App\Services;

use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class WalletService
{
    public function credit(User $user, float $amount, string $description, string $descriptionAr, ?Model $transactionable = null): WalletTransaction
    {
        return DB::transaction(function () use ($user, $amount, $description, $descriptionAr, $transactionable) {
            $balanceBefore = $user->wallet_balance;
            $balanceAfter = $balanceBefore + $amount;

            $user->increment('wallet_balance', $amount);

            return WalletTransaction::create([
                'reference' => WalletTransaction::generateReference(),
                'user_id' => $user->id,
                'type' => 'credit',
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'description' => $description,
                'description_ar' => $descriptionAr,
                'transactionable_id' => $transactionable?->id,
                'transactionable_type' => $transactionable ? get_class($transactionable) : null,
                'status' => 'completed',
            ]);
        });
    }

    public function debit(User $user, float $amount, string $description, string $descriptionAr, ?Model $transactionable = null): WalletTransaction
    {
        return DB::transaction(function () use ($user, $amount, $description, $descriptionAr, $transactionable) {
            $user->refresh();

            if ($user->wallet_balance < $amount) {
                throw new \Exception(__('Insufficient wallet balance. Required: ') . number_format($amount, 3) . ' JOD');
            }

            $balanceBefore = $user->wallet_balance;
            $balanceAfter = $balanceBefore - $amount;

            $user->decrement('wallet_balance', $amount);

            return WalletTransaction::create([
                'reference' => WalletTransaction::generateReference(),
                'user_id' => $user->id,
                'type' => 'debit',
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'description' => $description,
                'description_ar' => $descriptionAr,
                'transactionable_id' => $transactionable?->id,
                'transactionable_type' => $transactionable ? get_class($transactionable) : null,
                'status' => 'completed',
            ]);
        });
    }

    public function getBalance(User $user): float
    {
        return (float) $user->fresh()->wallet_balance;
    }

    public function formatJod(float $amount): string
    {
        return number_format($amount, 3) . ' JOD';
    }
}
