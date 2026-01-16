<?php

namespace App\Services;

use App\Enums\PaymentTypeEnum;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Transaction;
use App\Enums\TransactionTypeEnum;
use App\Enums\TransactionStatusEnum;
use App\Services\Payment\CashPaymentService;
use App\Services\Payment\PaymobService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{
    protected CashPaymentService $cashPaymentService;
    protected PaymobService $paymobService;

    public function __construct(
        CashPaymentService $cashPaymentService,
        PaymobService $paymobService
    ) {
        $this->cashPaymentService = $cashPaymentService;
        $this->paymobService = $paymobService;
    }

    /**
     * Create order from cart items
     *
     * @param int $userId
     * @param int $paymentType
     * @return array
     * @throws Exception
     */
    public function createOrder(int $userId, int $paymentType): array
    {
        DB::beginTransaction();

        try {
            // Get cart items
            $cartItems = OrderItem::cartItems($userId)
                ->with(['product', 'childProduct'])
                ->get();

            if ($cartItems->isEmpty()) {
                throw new Exception(__('trans.cart_is_empty'));
            }

            // Calculate total price with current prices
            $totalPrice = 0;
            $orderItems = [];

            foreach ($cartItems as $cartItem) {
                // Get current price from child product
                $childProduct = Product::find($cartItem->child_product_id);
                
                if (!$childProduct) {
                    throw new Exception(__('trans.child_product_not_found'));
                }

                if (!$childProduct->is_active) {
                    throw new Exception(__('trans.child_product_not_available'));
                }

                if ($childProduct->quantity < $cartItem->quantity) {
                    throw new Exception(__('trans.insufficient_quantity'));
                }

                // Use current price
                $currentPrice = $childProduct->price;
                $itemTotalPrice = $currentPrice * $cartItem->quantity;
                $totalPrice += $itemTotalPrice;

                $orderItems[] = [
                    'cart_item' => $cartItem,
                    'current_price' => $currentPrice,
                    'total_price' => $itemTotalPrice,
                ];
            }

            // Create order
            $order = Order::create([
                'user_id' => $userId,
                'total_price' => $totalPrice,
                'payment_type' => $paymentType,
            ]);

            // Convert cart items to order items with current prices
            foreach ($orderItems as $itemData) {
                $cartItem = $itemData['cart_item'];
                
                // Update cart item to order item
                $cartItem->update([
                    'order_id' => $order->id,
                    'price' => $itemData['current_price'],
                    'total_price' => $itemData['total_price'],
                ]);

                // Reduce product quantity
                $childProduct = Product::find($cartItem->child_product_id);
                $childProduct->decrement('quantity', $cartItem->quantity);
            }

            // Handle payment based on payment type
            $transaction = null;
            
            if ($paymentType === PaymentTypeEnum::CASH->value) {
                $transaction = $this->cashPaymentService->processPayment($order, $totalPrice);
            } elseif ($paymentType === PaymentTypeEnum::ONLINE->value) {
                $transaction = $this->paymobService->processPayment($order, $totalPrice);
            }

            DB::commit();

            return [
                'order' => $order->load(['items', 'user']),
                'transaction' => $transaction,
            ];

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Order Creation Error', [
                'user_id' => $userId,
                'payment_type' => $paymentType,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
