<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
use App\Http\Requests\Api\CreateRefundRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\RefundService;
use App\Traits\BaseApiResponseTrait;
use Illuminate\Http\JsonResponse;

/**
 * @group Api
 * @subgroup Refunds
 */
class RefundController extends BaseApiController
{
    use BaseApiResponseTrait;

    public array $relations;

    public function __construct(RefundService $service)
    {
        $this->service   = $service;
        $this->relations = ['items.product', 'items.childProduct', 'refundOrders.orderItems'];
        parent::__construct($service, OrderResource::class);
    }

    /**
     * Create a partial refund for specific items from an order
     * @authenticated
     * @bodyParam order_id integer required The ID of the order to refund
     * @bodyParam refund_type string required Refund type (wallet, cash, online)
     * @bodyParam note string optional Refund note
     * @bodyParam items array required Array of items to refund
     * @bodyParam items.*.order_item_id integer required Order item ID
     * @bodyParam items.*.quantity integer required Quantity to refund
     * @return JsonResponse
     */
    public function createRefund(CreateRefundRequest $request): JsonResponse
    {
    
        try {
            $user = auth('sanctum')->user();
            $order = $this->service->find($request->order_id, $this->relations);

            $refundOrder = $this->service->createRefund($order, $request->all());
            return $this->respondWithModel($refundOrder);
        } catch (\Exception $e) {
            return $this->respondWithError($e->getMessage());
        }
    }

    /**
     * Get refundable orders for the authenticated user
     * @authenticated
     * @return JsonResponse
     */
    public function getRefundableOrders(): JsonResponse
    {
        try {
            $user = auth('sanctum')->user();
            $orders = $this->service->getRefundableOrders($user->id, [
                'relations' => ['items.product', 'items.childProduct', 'refundOrders']
            ]);

            return $this->respondWithCollection($orders);
        } catch (\Exception $e) {
            return $this->respondWithError($e->getMessage());
        }
    }

    /**
     * Get refundable items for a specific order
     * @authenticated
     * @urlParam order integer required Order ID
     * @return JsonResponse
     */
    public function getRefundableItems($orderId): JsonResponse
    {
        try {
            $user = auth('sanctum')->user();
            $order = $this->service->find($orderId, $this->relations);
            $refundableItems = $this->service->getRefundableItems($order);
            return $this->respondWithCollection($refundableItems);
        } catch (\Exception $e) {
            return $this->respondWithError($e->getMessage());
        }
    }
}