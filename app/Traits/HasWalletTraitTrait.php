<?php

namespace App\Traits;

use App\Enums\WalletTransactionTypeEnum;
use App\Models\Wallet;
use App\Services\WalletService;
use Exception;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\DB;

trait HasWalletTraitTrait
{
    public static function bootHasWalletTrait(): void
    {
        static::created(function ($model) {
            $model->createWallet();
        });
    }
    public function wallet(): MorphOne
    {
        return $this->morphOne(Wallet::class, 'walletable');
    }
    public function createWallet(array $attributes = []): Wallet|bool
    {

        try {
            DB::beginTransaction();

            $wallet = $this->wallet()->create([
                'balance' => $attributes['balance'] ?? 0,
                'status'  => $attributes['status'] ?? 1,
            ]);

            // create initial credit transaction only if there is a starting balance
            if (!empty($attributes['balance'])) {
                /** @var WalletService $walletService */
                $walletService = app(WalletService::class);
                $walletService->credit(
                    $wallet,
                    (float)$attributes['balance'],
                    'admin',
                    auth()->guard('admin')->id() ?? null,
                    __('trans.initial_wallet_balance')
                );
            }

            DB::commit();
            return $wallet;
        } catch (Exception $exception) {
            DB::rollBack();
            return false;
        }
    }
    public function getWallet(): Wallet|null
    {
        return $this->wallet ?? null;
    }
    public function addToWallet($balance = 0, $type = WalletTransactionTypeEnum::add->value, $wallet = null): bool
    {
        try {
            if (!$wallet) {
                $wallet = $this->getWallet();
            }
            if (!$wallet) {
                $wallet = $this->createWallet();
            }

            /** @var WalletService $walletService */
            $walletService = app(WalletService::class);
            $walletService->credit(
                $wallet,
                (float)$balance,
                'admin',
                auth()->guard('admin')->id() ?? null
            );

            return true;
        } catch (Exception $exception) {
            return false;
        }
    }
    public function deductFromWallet($balance, $type = WalletTransactionTypeEnum::deduct->value, $wallet = null): bool
    {
        try {
            if (!$wallet) {
                $wallet = $this->getWallet();
            }

            /** @var WalletService $walletService */
            $walletService = app(WalletService::class);
            $walletService->debit(
                $wallet,
                (float)$balance,
                'admin',
                auth()->guard('admin')->id() ?? null
            );

            return true;
        } catch (Exception $exception) {
            return false;
        }
    }
}
