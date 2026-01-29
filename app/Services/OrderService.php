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
use App\Services\WalletService;
use App\Services\ProductQuantityService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{
    protected CashPaymentService $cashPaymentService;
    protected PaymobService $paymobService;
    protected WalletService $walletService;
    protected ProductQuantityService $productQuantityService;

    public function __construct(
        CashPaymentService $cashPaymentService,
        PaymobService $paymobService,
        WalletService $walletService,
        ProductQuantityService $productQuantityService
    ) {
        $this->cashPaymentService = $cashPaymentService;
        $this->paymobService = $paymobService;
        $this->walletService = $walletService;
        $this->productQuantityService = $productQuantityService;
    }

    public function createOrder(int $userId, int $paymentType): array
    {
        return DB::transaction(function () use ($userId, $paymentType) {
            $cartItems = $this->getValidatedCartItems($userId);
            $totalPrice = $this->calculateTotalPrice($cartItems);
            $order = $this->createOrderRecord($userId, $totalPrice, $paymentType);

            $this->convertCartItemsToOrderItems($cartItems, $order);
            $this->reduceProductQuantities($cartItems, $order);
            $transaction = $this->processPayment($order, $totalPrice, $paymentType);

            return [
                'order' => $order->load(['items', 'user']),
                'transaction' => $transaction,
            ];
        });
    }

    private function getValidatedCartItems(int $userId)
    {
        $cartItems = OrderItem::cartItems($userId)
            ->with(['product', 'childProduct'])
            ->get();

        if ($cartItems->isEmpty()) {
            throw new Exception(__('trans.cart_is_empty'));
        }

        return $cartItems;
    }

    private function calculateTotalPrice($cartItems): float
    {
        $totalPrice = 0;

        foreach ($cartItems as $cartItem) {
            $childProduct = $this->validateChildProduct($cartItem);
            $itemTotalPrice = $childProduct->price * $cartItem->quantity;
            $totalPrice += $itemTotalPrice;
        }

        return $totalPrice;
    }

    private function validateChildProduct($cartItem)
    {
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

        return $childProduct;
    }

    private function createOrderRecord(int $userId, float $totalPrice, int $paymentType): Order
    {
        return Order::create([
            'user_id' => $userId,
            'total_price' => $totalPrice,
            'payment_type' => $paymentType,
        ]);
    }

    private function convertCartItemsToOrderItems($cartItems, Order $order): void
    {
        foreach ($cartItems as $cartItem) {
            $childProduct = Product::find($cartItem->child_product_id);
            $currentPrice = $childProduct->price;
            $itemTotalPrice = $currentPrice * $cartItem->quantity;

            $cartItem->update([
                'order_id' => $order->id,
                'price' => $currentPrice,
                'total_price' => $itemTotalPrice,
            ]);
        }
    }

    private function reduceProductQuantities($cartItems, Order $order): void
    {
        foreach ($cartItems as $cartItem) {
            $childProduct = Product::find($cartItem->child_product_id);
            $this->productQuantityService->debit(
                $childProduct,
                $cartItem->quantity,
                'order',
                'order',
                $order->id,
                __('trans.quantity_order_for_order', ['order_id' => $order->id])
            );
        }
    }

    private function processPayment(Order $order, float $totalPrice, int $paymentType)
    {
        if ($paymentType === PaymentTypeEnum::CASH->value) {
            return $this->cashPaymentService->processPayment($order, $totalPrice);
        }

        if ($paymentType === PaymentTypeEnum::ONLINE->value) {
            return $this->paymobService->processPayment($order, $totalPrice);
        }

        if ($paymentType === PaymentTypeEnum::WALLET->value) {
            return $this->processWalletPayment($order, $totalPrice);
        }

        return null;
    }

    private function processWalletPayment(Order $order, float $totalPrice)
    {
        $user = $order->user;
        $wallet = $user->getWallet();

        $this->validateWallet($wallet, $totalPrice);

        $walletTransaction = $this->walletService->debit(
            $wallet,
            $totalPrice,
            'order',
            $order->id,
            __('trans.wallet_payment_for_order', ['order_id' => $order->id])
        );

        return Transaction::create([
            'transactionable_type' => Order::class,
            'transactionable_id' => $order->id,
            'amount' => $totalPrice,
            'currency' => 'egp',
            'payment_method' => PaymentTypeEnum::WALLET->value,
            'type' => TransactionTypeEnum::WALLET_DEPOSIT->value,
            'status' => TransactionStatusEnum::COMPLETED->value,
            'metadata' => [
                'wallet_transaction_id' => $walletTransaction->id,
                'payment_type' => 'wallet'
            ],
            'processed_at' => now(),
        ]);
    }

    private function validateWallet($wallet, float $totalPrice): void
    {
        if (!$wallet) {
            throw new Exception(__('trans.wallet_not_found'));
        }

        if ($wallet->status !== 1) {
            throw new Exception(__('trans.wallet_suspended'));
        }

        if ($wallet->balance < $totalPrice) {
            throw new Exception(__('trans.insufficient_wallet_balance'));
        }
    }
}
