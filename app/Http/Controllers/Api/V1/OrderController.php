<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CreateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;
use App\Traits\BaseApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * @group Api
 * @subgroup Orders
 */
class OrderController extends Controller
{
    use BaseApiResponseTrait;

    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Create order from cart
     * @authenticated
     * @bodyParam payment_type integer required Payment type (1=Online, 2=Cash, 3=Wallet)
     * @return JsonResponse
     */
    public function createOrder(CreateOrderRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $user = auth('sanctum')->user();
            $paymentType = $request->payment_type;

            $result = $this->orderService->createOrder($user->id, $paymentType);
            DB::commit();
            return $this->respondWithSuccess(
                __('trans.order_created_successfully'),
                [
                    'order' => new OrderResource($result['order']),
                    'transaction' => $result['transaction'],
                ]
            );

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->respondWithError($e->getMessage());
        }
    }
}
