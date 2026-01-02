<?php

namespace App\Traits;

use App\Enums\WalletTransactionTypeEnum;
use App\Models\Wallet;
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
            $wallet = $this->wallet()->create($attributes);

            if ($wallet)
                $wallet->transactions()->create([
                    'type' => WalletTransactionTypeEnum::add->value,
                    'balance' => $attributes['balance'] ?? 0,
                    'admin_id' => auth()->guard('admin')->user()->id ?? null,
                ]);

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
            DB::beginTransaction();
            if (!$wallet) {
                $wallet = $this->getWallet();
            }
            if (!$wallet) {
                $wallet = $this->createWallet(['balance' => $balance]);
            }

            $wallet->increment('balance', $balance);
            $wallet->transactions()->create([
                'type' => $type,
                'balance' => $balance,
                'admin_id' => auth()->guard('admin')->user()->id ?? null,
            ]);
            DB::commit();
            return true;
        } catch
        (Exception $exception) {
            DB::rollBack();
            return false;
        }
    }
    public function deductFromWallet($balance, $type = WalletTransactionTypeEnum::deduct->value, $wallet = null): bool
    {
        try {
            DB::beginTransaction();
            if (!$wallet) {
                $wallet = $this->getWallet();
            }
            $wallet->decrement('balance', $balance);
            $wallet->transactions()->create([
                'type' => $type,
                'balance' => $balance
            ]);
            DB::commit();
            return true;
        } catch
        (Exception $exception) {
            DB::rollBack();
            return false;
        }
    }
}
