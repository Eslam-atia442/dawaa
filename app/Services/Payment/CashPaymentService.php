<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\Transaction;
use App\Enums\TransactionTypeEnum;
use App\Enums\TransactionStatusEnum;
use App\Enums\PaymentTypeEnum;
use Exception;
use Illuminate\Support\Facades\Log;

class CashPaymentService
{
    /**
     * Process cash payment and create transaction
     *
     * @param Order $order
     * @param float $amount
     * @return Transaction
     * @throws Exception
     */
    public function processPayment(Order $order, float $amount): Transaction
    {
        try {
            $transaction = Transaction::create([
                'transactionable_type' => Order::class,
                'transactionable_id' => $order->id,
                'initiated_by_type' => get_class($order->user),
                'initiated_by_id' => $order->user_id,
                'type' => TransactionTypeEnum::CASH_DEPOSIT,
                'status' => TransactionStatusEnum::COMPLETED, // Cash is immediately completed
                'amount' => $amount,
                'currency' => 'egp',
                'payment_method' => PaymentTypeEnum::CASH,
                'transaction_reference' => Transaction::generateReference(),
                'description' => __('trans.cash_payment_for_order', ['order_id' => $order->id]),
                'processed_at' => now(),
            ]);

            return $transaction;

        } catch (Exception $e) {
            Log::error('Cash Payment Error', [
                'order_id' => $order->id,
                'amount' => $amount,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
