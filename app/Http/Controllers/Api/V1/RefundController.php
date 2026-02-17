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

    protected RefundService $refundService;

    public function __construct(RefundService $refundService)
    {
        $this->refundService = $refundService;
        parent::__construct($refundService, OrderResource::class);
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
            $order = app(OrderService::class)->find($request->order_id, ['items.product', 'items.childProduct', 'user', 'refundOrders']);

            $refundOrder = $this->refundService->createRefund($order, [
                'refund_type' => $request->refund_type,
                'note' => $request->note,
                'items' => $request->items,
            ]);

            return $this->respondWithSuccess(
                __('trans.refund_request_submitted'),
                [
                    'refund_order' => new OrderResource($refundOrder),
                    'original_order' => new OrderResource($order),
                ]
            );
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
            $orders = $this->refundService->getRefundableOrders($user->id, [
                'relations' => ['items.product', 'items.childProduct', 'refundOrders']
            ]);

            return $this->respondWithSuccess(
                __('trans.refundable_orders_retrieved_successfully'),
                [
                    'orders' => OrderResource::collection($orders),
                ]
            );
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
            $order = app(OrderService::class)->find($orderId, [
                'items.product',
                'items.childProduct',
                'refundOrders.orderItems'
            ]);

            if ($order->user_id !== $user->id) {
                return $this->respondWithError(__('trans.order_not_found'));
            }

            $refundableItems = $this->refundService->getRefundableItems($order);

            return $this->respondWithSuccess(
                __('trans.refundable_items_retrieved_successfully'),
                [
                    'order' => new OrderResource($order),
                    'refundable_items' => $refundableItems,
                ]
            );
        } catch (\Exception $e) {
            return $this->respondWithError($e->getMessage());
        }
    }
}