<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
use App\Http\Resources\WalletResource;
use App\Http\Resources\WalletTransactionResource;
use App\Services\WalletService;
use App\Traits\BaseApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group Api
 * @subgroup Wallet
 */
class WalletController extends BaseApiController
{
    use BaseApiResponseTrait;

    protected WalletService $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
        parent::__construct($walletService, WalletResource::class);
    }

    /**
     * Get user wallet.
     * @authenticated
     * @return JsonResponse
     */
    public function getWallet(): JsonResponse
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return $this->respondWithError(__('trans.user_not_found'), 404);
        }

        $wallet = $user->wallet;

        if (!$wallet) {
            $wallet = $user->createWallet();
        }

        return $this->respondWithSuccess(
            __('trans.wallet_retrieved_successfully'),
            [
                'wallet' => new WalletResource($wallet)
            ]
        );
    }

    /**
     * Get wallet transaction history.
     * @authenticated
     * @queryParam page Page number for pagination.
     * @queryParam limit Number of items per page.
     * @queryParam type Filter by transaction type (1=credit, 2=debit).
     * @queryParam createdAtMin Filter transactions from this date (Y-m-d).
     * @queryParam createdAtMax Filter transactions until this date (Y-m-d).
     * @return JsonResponse
     */
    public function getHistory(Request $request): mixed
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return $this->respondWithError(__('trans.user_not_found'), 404);
        }

        $filters = request()->all();
        $filters['wallet'] = $user->wallet->id;
 
        $transactions = $this->walletService->search($filters, ['wallet'], []);
        $transactions = WalletTransactionResource::collection($transactions);

        return $transactions;
    
    }
}
