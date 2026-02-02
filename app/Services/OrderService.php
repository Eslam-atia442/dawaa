<?php

namespace App\Services;

use App\Enums\PaymentTypeEnum;
use App\Models\Cart;
use App\Models\ChildProduct;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Repositories\Contracts\BaseContract;
use App\Repositories\Contracts\OrderContract;
use App\Services\Payment\CashPaymentService;
use App\Services\Payment\PaymobService;
use App\Services\ProductQuantityService;
use App\Services\WalletService;
use Exception;
use Illuminate\Support\Facades\DB;

class OrderService extends BaseService
{

    protected BaseContract $repository;
    protected WalletService $walletService;
    protected ProductQuantityService $productQuantityService;
    protected PaymobService $paymobService;
    protected CashPaymentService $cashPaymentService;

    public function __construct(
        OrderContract $repository,
        WalletService $walletService,
        ProductQuantityService $productQuantityService,
        PaymobService $paymobService,
        CashPaymentService $cashPaymentService
    ) {
        $this->repository = $repository;
        $this->walletService = $walletService;
        $this->productQuantityService = $productQuantityService;
        $this->paymobService = $paymobService;
        $this->cashPaymentService = $cashPaymentService;
        parent::__construct($repository);
    }

    public function create($request)
    {
        DB::beginTransaction();
        $object = $this->repository->create($request);
        DB::commit();
        return $object;
    }

    public function update($order, $request)
    {
        return $this->repository->update($order, $request);
    }

    public function remove($order)
    {
        return $this->repository->remove($order);
    }

    public function createOrder($userId, $paymentType)
    {
        $cartItems = OrderItem::where('user_id', $userId)->whereNull('order_id')->with(['childProduct.parent', 'childProduct'])->get();

        if ($cartItems->isEmpty()) {
            throw new Exception(__('trans.cart_is_empty'));
        }

        $totalPrice = 0;
        $validatedCartItems = [];

        foreach ($cartItems as $cartItem) {
            if (!$cartItem->childProduct) {
                throw new Exception(__('trans.child_product_not_found'));
            }

            $childProduct = $cartItem->childProduct;
            $quantity = $cartItem->quantity;
            $price = $childProduct->price;

            if ($quantity > $childProduct->quantity) {
                throw new Exception(__('trans.insufficient_quantity', [
                    'product' => $childProduct->parent->name ?? 'Product',
                    'available' => $childProduct->quantity,
                    'requested' => $quantity
                ]));
            }

            $totalPrice += $quantity * $price;
            $validatedCartItems[] = [
                'child_product' => $childProduct,
                'quantity' => $quantity,
                'price' => $price,
            ];
        }

        DB::beginTransaction();
        try {
            $order = Order::create([
                'user_id' => $userId,
                'total_price' => $totalPrice,
                'payment_type' => $paymentType,
            ]);

            foreach ($validatedCartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['child_product']->parent_id,
                    'child_product_id' => $item['child_product']->id,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);

                $this->productQuantityService->debit(
                    $item['child_product'],
                    $item['quantity'],
                    'order',
                    Order::class,
                    $order->id,
                    __('trans.quantity_change_for_order', ['order_id' => $order->id])
                );
            }

            $transaction = $this->processPayment($order, $paymentType);

            OrderItem::where('user_id', $userId)->whereNull('order_id')->delete();

            DB::commit();

            return [
                'order' => $order->load(['items', 'user']),
                'transaction' => $transaction,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function processPayment(Order $order, $paymentType)
    {
        switch ($paymentType) {
            case PaymentTypeEnum::WALLET->value:
                return $this->walletService->debit(
                    $order->user->wallet,
                    $order->total_price,
                    Order::class,
                    $order->id,
                    __('trans.wallet_payment_for_order', ['order_id' => $order->id])
                );
            case PaymentTypeEnum::ONLINE->value:
                return $this->paymobService->processPayment($order, $order->total_price);
            case PaymentTypeEnum::CASH->value:
                return $this->cashPaymentService->processPayment($order, $order->total_price);
            default:
                throw new Exception(__('trans.invalid_payment_type'));
        }
    }

}
