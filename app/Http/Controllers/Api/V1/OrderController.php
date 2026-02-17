<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
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
class OrderController extends BaseApiController
{
    use BaseApiResponseTrait;

    public array $relations;

    public function __construct(OrderService $service)
    {
        $this->service   = $service;
        $this->relations = ['items', 'items.product', 'items.childProduct.parent'];
        parent::__construct($service, OrderResource::class);
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

            $result = $this->service->createOrder($user->id, $paymentType);
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

    /**
     * Get user's orders
     * @authenticated
     * @queryParam page integer Page number
     * @queryParam limit integer Items per page
     * @return JsonResponse
     */
    public function myOrders(): JsonResponse
    {

        request()->merge(['myOrders' => true]);
        $orders = $this->service->search(request()->all(), $this->relations, []);
        return $this->respondWithCollection($orders);
    }

    /**
     * Get order details
     * @authenticated
     * @urlParam order integer required Order ID
     * @return JsonResponse
     */


    public function show($orderId): JsonResponse
    {
        $user = auth('sanctum')->user();
        $order = $this->service->find($orderId, $this->relations);

        // Ensure user can only view their own orders
        if (!$order || $order->user_id !== $user->id) {
            return $this->respondWithError(__('trans.unauthorized'), 403);
        }

        return $this->respondWithSuccess(
            __('trans.order_details_retrieved_successfully'),
            ['order' => new OrderResource($order)]
        );
    }
}
