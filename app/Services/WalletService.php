<?php

namespace App\Services;

use App\Enums\WalletTransactionTypeEnum;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Exception;
use Illuminate\Support\Facades\DB;

class WalletService
{
    /**
     * Credit amount to wallet (increase balance).
     *
     * @throws Exception
     */
    public function credit(
        Wallet $wallet,
        float $amount,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $description = null
    ): WalletTransaction {
        if ($amount <= 0) {
            throw new Exception('Amount must be greater than zero.');
        }

        return DB::transaction(function () use ($wallet, $amount, $referenceType, $referenceId, $description) {
            $balanceBefore = $wallet->balance;
            $balanceAfter  = $balanceBefore + $amount;

            $wallet->update(['balance' => $balanceAfter]);

            return $wallet->transactions()->create([
                'amount'         => $amount,
                'type'           => WalletTransactionTypeEnum::add->value,
                'balance_before' => $balanceBefore,
                'balance_after'  => $balanceAfter,
                'reference_type' => $referenceType,
                'reference_id'   => $referenceId,
                'description'    => $description,
                'admin_id'       => auth()->guard('admin')->id() ?? null,
            ]);
        });
    }

    /**
     * Debit amount from wallet (decrease balance).
     *
     * @throws Exception
     */
    public function debit(
        Wallet $wallet,
        float $amount,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $description = null
    ): WalletTransaction {
        if ($amount <= 0) {
            throw new Exception('Amount must be greater than zero.');
        }

        return DB::transaction(function () use ($wallet, $amount, $referenceType, $referenceId, $description) {
            $balanceBefore = $wallet->balance;
            if ($balanceBefore < $amount) {
                throw new Exception(__('trans.insufficient_wallet_balance'));
            }

            $balanceAfter = $balanceBefore - $amount;

            $wallet->update(['balance' => $balanceAfter]);

            return $wallet->transactions()->create([
                'amount'         => $amount,
                'type'           => WalletTransactionTypeEnum::deduct->value,
                'balance_before' => $balanceBefore,
                'balance_after'  => $balanceAfter,
                'reference_type' => $referenceType,
                'reference_id'   => $referenceId,
                'description'    => $description,
                'admin_id'       => auth()->guard('admin')->id() ?? null,
            ]);
        });
    }

    /**
     * Refund amount to wallet (wrapper around credit with refund context).
     *
     * @throws Exception
     */
    public function refund(
        Wallet $wallet,
        float $amount,
        ?string $referenceType = 'refund',
        ?int $referenceId = null,
        ?string $description = null
    ): WalletTransaction {
        $description = $description ?? __('trans.wallet_refund');

        return $this->credit(
            $wallet,
            $amount,
            $referenceType,
            $referenceId,
            $description
        );
    }

    /**
     * Freeze wallet (suspend).
     */
    public function freeze(Wallet $wallet): Wallet
    {
        $wallet->update(['status' => 0]);
        return $wallet;
    }

    /**
     * Unfreeze wallet (activate).
     */
    public function unfreeze(Wallet $wallet): Wallet
    {
        $wallet->update(['status' => 1]);
        return $wallet;
    }
}

