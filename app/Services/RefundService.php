<?php

namespace App\Services;

use App\Enums\OrderStatusEnum;
use App\Enums\RefundTypeEnum;
use App\Models\Order;
use App\Repositories\Contracts\BaseContract;
use App\Repositories\Contracts\RefundContract;
use Exception;
use Illuminate\Support\Facades\DB;

class RefundService extends BaseService
{
    protected BaseContract $repository;
    protected WalletService $walletService;
    protected ProductQuantityService $productQuantityService;

    public function __construct(
        RefundContract $repository,
        WalletService $walletService,
        ProductQuantityService $productQuantityService
    ) {
        $this->repository = $repository;
        $this->walletService = $walletService;
        $this->productQuantityService = $productQuantityService;
        parent::__construct($repository);
    }

    public function createRefund(Order $originalOrder, array $refundData): Order
    {

        $this->validateRefundEligibility($originalOrder);

        $this->validateRefundType($refundData['refund_type']);

        $validatedItems = $this->validateRefundItems($originalOrder, $refundData['items']);
        
        $refundTotal = $this->calculateRefundTotal($validatedItems);
        $refundTypeValue = $this->getRefundTypeEnumValue($refundData['refund_type']);
        DB::beginTransaction();
        try {
            $refundOrderData = [
                'total_price' => $refundTotal,
                'payment_type' => $originalOrder->payment_type,
                'refund_type' => $refundTypeValue,
                'note' => $refundData['note'] ?? null,
                'status' => OrderStatusEnum::REFUND_REQUESTED->value,
            ];

            $refundOrder = $this->repository->createRefundOrder($originalOrder, $refundOrderData);
            $this->createPartialRefundOrderItems($refundOrder, $validatedItems);




            DB::commit();

            return $refundOrder->load(['items', 'user']);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function validateRefundEligibility(Order $order): void
    {
     
        
        if ($order->user_id != auth('sanctum')->id()) {
            throw new Exception(__('trans.order_not_found'));
        }

        $oldRefundedOrder = $order->refundOrders()->exists();
        if ($oldRefundedOrder) {
            throw new Exception(__('trans.order_has_old_refunded_order'));
        }

        if ($order->items()->count() === 0) {
            throw new Exception(__('trans.order_has_no_items'));
        }

        $isCompletelyRefunded = $this->isOrderCompletelyRefunded($order);
        if ($isCompletelyRefunded) {
            throw new Exception(__('trans.order_already_refunded'));
        }
    }

    private function isOrderCompletelyRefunded(Order $order): bool
    {
        foreach ($order->items as $orderItem) {
            $refundedQuantity = $order->refundOrders()
                ->join('order_items as refund_items', 'refund_items.order_id', '=', 'orders.id')
                ->where('refund_items.product_id', $orderItem->product_id)
                ->where('refund_items.child_product_id', $orderItem->child_product_id)
                ->sum('refund_items.quantity');

            if ($refundedQuantity < $orderItem->quantity) {
                return false;
            }
        }

        return true;
    }

    private function validateRefundType(string $refundType): void
    {
        $refundConfig = config('refund.types.' . $refundType);

        if (!$refundConfig) {
            throw new Exception(__('trans.invalid_refund_type'));
        }

        if (!$refundConfig['can_be_refunded']) {
            throw new Exception(__('trans.refund_type_not_allowed', ['type' => $refundConfig['label']]));
        }
    }

    private function validateRefundItems(Order $originalOrder, array $refundItems): array
    {
        $validatedItems = [];

        foreach ($refundItems as $refundItem) {
            $orderItem = $originalOrder->items()->find($refundItem['order_item_id']);

            if (!$orderItem) {
                throw new Exception(__('trans.order_item_not_found'));
            }

            $alreadyRefunded = $originalOrder->refundOrders()
                ->join('order_items as refund_items', 'refund_items.order_id', '=', 'orders.id')
                ->where('refund_items.product_id', $orderItem->product_id)
                ->where('refund_items.child_product_id', $orderItem->child_product_id)
                ->sum('refund_items.quantity');

            $availableQuantity = $orderItem->quantity - $alreadyRefunded;

            if ($refundItem['quantity'] > $availableQuantity) {
                throw new Exception(__('trans.refund_quantity_exceeds_available', [
                    'product' => $orderItem->product->name ?? 'Unknown Product',
                    'available' => $availableQuantity,
                    'requested' => $refundItem['quantity']
                ]));
            }

            if ($refundItem['quantity'] <= 0) {
                throw new Exception(__('trans.invalid_refund_quantity'));
            }

            $validatedItems[] = [
                'order_item' => $orderItem,
                'quantity' => $refundItem['quantity'],
                'price' => $orderItem->price,
            ];
        }

        return $validatedItems;
    }

    private function calculateRefundTotal(array $validatedItems): float
    {
        $total = 0;

        foreach ($validatedItems as $item) {
            $total += $item['quantity'] * $item['price'];
        }

        return $total;
    }

    private function createPartialRefundOrderItems(Order $refundOrder, array $validatedItems): void
    {
        foreach ($validatedItems as $item) {
            $refundOrder->items()->create([
                'product_id' => $item['order_item']->product_id,
                'child_product_id' => $item['order_item']->child_product_id,
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);
        }
    }

    private function refundProductQuantitiesPartial(array $validatedItems): void
    {
        foreach ($validatedItems as $item) {
            if ($item['order_item']->childProduct) {
                $this->productQuantityService->credit(
                    $item['order_item']->childProduct,
                    $item['quantity'],
                    'refund',
                    Order::class,
                    $item['order_item']->order_id,
                    __('trans.refund_quantity_for_order', ['order_id' => $item['order_item']->order_id])
                );
            }
        }
    }

    public function approveRefund(Order $refundOrder): Order
    {
        if ($refundOrder->status->value !== OrderStatusEnum::REFUND_REQUESTED->value) {
            throw new Exception(__('trans.refund_not_pending'));
        }

        DB::beginTransaction();
        try {
            $noteData = json_decode($refundOrder->note, true);
            $refundItemsData = $noteData['refund_items'] ?? [];

            // Fallback: If refund items data is not stored in note, reconstruct from refund order items
            if (empty($refundItemsData)) {
                $refundItemsData = $this->reconstructRefundItemsData($refundOrder);
                if (empty($refundItemsData)) {
                    throw new Exception(__('trans.refund_items_not_found'));
                }
            }

            $this->processRefund($refundOrder->parentOrder, $refundOrder, $refundOrder->refund_type->value);
            $this->refundProductQuantitiesPartial($refundItemsData);

            $originalNote = $noteData['original_note'] ?? '';
            $refundOrder->update([
                'status' => OrderStatusEnum::REFUND_APPROVED->value,
                'note' => $originalNote
            ]);

            DB::commit();

            return $refundOrder->fresh(['items', 'user']);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function rejectRefund(Order $refundOrder): Order
    {
        if ($refundOrder->status->value !== OrderStatusEnum::REFUND_REQUESTED->value) {
            throw new Exception(__('trans.refund_not_pending'));
        }

        $noteData = json_decode($refundOrder->note, true);
        $originalNote = $noteData['original_note'] ?? '';

        $refundOrder->update([
            'status' => OrderStatusEnum::REFUND_REJECTED->value,
            'note' => $originalNote
        ]);

        return $refundOrder->fresh();
    }

    private function reconstructRefundItemsData(Order $refundOrder): array
    {
        $refundItemsData = [];

        foreach ($refundOrder->items as $refundItem) {
            // Find the corresponding original order item to get pricing info
            $originalOrderItem = $refundOrder->parentOrder->items()
                ->where('product_id', $refundItem->product_id)
                ->where('child_product_id', $refundItem->child_product_id)
                ->first();

            if ($originalOrderItem) {
                $refundItemsData[] = [
                    'order_item' => $originalOrderItem,
                    'quantity' => $refundItem->quantity,
                    'price' => $refundItem->price,
                ];
            }
        }

        return $refundItemsData;
    }

    private function getRefundTypeEnumValue(string $refundType): int
    {
        $refundConfig = config('refund.types.' . $refundType);

        if (!$refundConfig || !isset($refundConfig['enum_value'])) {
            throw new Exception(__('trans.invalid_refund_type'));
        }

        return $refundConfig['enum_value'];
    }

    private function createRefundOrderItems(Order $refundOrder, Order $originalOrder): void
    {
        foreach ($originalOrder->items as $originalItem) {
            $refundOrder->items()->create([
                'product_id' => $originalItem->product_id,
                'child_product_id' => $originalItem->child_product_id,
                'quantity' => $originalItem->quantity,
                'price' => $originalItem->price,
            ]);
        }
    }

    private function processRefund(Order $originalOrder, Order $refundOrder, int $refundType): void
    {
        switch ($refundType) {
            case RefundTypeEnum::WALLET->value:
                $this->processWalletRefund($originalOrder, $refundOrder);
                break;
            case RefundTypeEnum::CASH->value:
                break;
            case RefundTypeEnum::ONLINE->value:
                break;
            default:
                throw new Exception(__('trans.unsupported_refund_type'));
        }
    }

    private function processWalletRefund(Order $originalOrder, Order $refundOrder): void
    {
        $user = $originalOrder->user;

        if (!$user->wallet) {
            throw new Exception(__('trans.user_has_no_wallet'));
        }

        $this->walletService->credit(
            $user->wallet,
            $refundOrder->total_price,
            'Refund Order',
            $refundOrder->id,
            __('trans.refund_for_order', ['order_id' => $originalOrder->id])
        );
    }

    /**
     * Refund product quantities back to inventory.
     *
     * @param Order $originalOrder
     */
    private function refundProductQuantities(Order $originalOrder): void
    {
        foreach ($originalOrder->items as $item) {
            if ($item->childProduct) {
                // Refund quantity back to child product
                $this->productQuantityService->credit(
                    $item->childProduct,
                    $item->quantity,
                    'refund',
                    Order::class,
                    $originalOrder->id,
                    __('trans.refund_quantity_for_order', ['order_id' => $originalOrder->id])
                );
            }
        }
    }

    /**
     * Get refundable orders for a user.
     *
     * @param int $userId
     * @param array $filters
     * @return mixed
     */
    public function getRefundableOrders(int $userId, array $filters = [])
    {
        return $this->repository->getRefundableOrders($userId, $filters);
    }

    /**
     * Get refundable items for an order with available quantities.
     *
     * @param Order $order
     * @return array
     */
    public function getRefundableItems(Order $order): array
    {
        $refundableItems = [];

        foreach ($order->items as $orderItem) {
            // Calculate already refunded quantity for this item
            $alreadyRefunded = $order->refundOrders()
                ->join('order_items as refund_items', 'refund_items.order_id', '=', 'orders.id')
                ->where('refund_items.product_id', $orderItem->product_id)
                ->where('refund_items.child_product_id', $orderItem->child_product_id)
                ->sum('refund_items.quantity');

            $availableQuantity = $orderItem->quantity - $alreadyRefunded;

            if ($availableQuantity > 0) {
                $refundableItems[] = [
                    'order_item_id' => $orderItem->id,
                    'product_name' => $orderItem->product->name ?? 'Unknown Product',
                    'quantity_ordered' => $orderItem->quantity,
                    'quantity_refunded' => $alreadyRefunded,
                    'quantity_available' => $availableQuantity,
                    'price' => $orderItem->price,
                    'child_product' => $orderItem->childProduct,
                ];
            }
        }

        return $refundableItems;
    }
}