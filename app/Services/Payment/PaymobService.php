<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\Transaction;
use App\Enums\TransactionTypeEnum;
use App\Enums\TransactionStatusEnum;
use App\Enums\PaymentTypeEnum;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class PaymobService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $secretKey;
    protected string $publicKey;
    protected array $integrationId;
    protected string $currency;
    protected string $notificationUrl;
    protected string $redirectionUrl;
    protected ?string $token = null;

    public function __construct()
    {
        $this->baseUrl = config('services.paymob.base_url');
        $this->apiKey = config('services.paymob.api_key');

        $this->secretKey = config('services.paymob.secret_key');
        $this->publicKey = config('services.paymob.public_key');
        $this->integrationId = config('services.paymob.integration_id');
        $this->currency = config('services.paymob.currency', 'EGP');
        $this->notificationUrl = config('services.paymob.notification_url');
        $this->redirectionUrl = config('services.paymob.redirection_url');
    }

    /**
     * Authenticate and get token
     *
     * @return string
     * @throws Exception
     */
    protected function authenticate(): string
    {
        try {
            $response = Http::post($this->baseUrl . '/api/auth/tokens', [
                'api_key' => $this->apiKey,
            ]);

            if (!$response->successful()) {
                $error = $response->json();
                Log::error('Paymob Authentication Error', [
                    'status' => $response->status(),
                    'response' => $error,
                ]);
                throw new Exception('Paymob Authentication Failed: ' . ($error['detail'] ?? $error['message'] ?? 'Unknown error'));
            }

            $result = $response->json();
            $this->token = $result['token'] ?? null;

            if (!$this->token) {
                throw new Exception('Paymob Authentication Failed: Token not received');
            }


            return $this->token;
        } catch (Exception $e) {
            Log::error('Paymob Authentication Exception', [
                'message' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Process online payment and create transaction
     *
     * @param Order $order
     * @param float $amount
     * @return Transaction
     * @throws Exception
     */
    public function processPayment(Order $order, float $amount): Transaction
    {
        try {
            // Create transaction first
            $transaction = Transaction::create([
                'transactionable_type' => Order::class,
                'transactionable_id' => $order->id,
                'initiated_by_type' => get_class($order->user),
                'initiated_by_id' => $order->user_id,
                'type' => TransactionTypeEnum::ONLINE_DEPOSIT,
                'status' => TransactionStatusEnum::PENDING,
                'amount' => $amount,
                'currency' => 'egp',
                'payment_method' => PaymentTypeEnum::ONLINE,
                'transaction_reference' => Transaction::generateReference(),
                'description' => __('trans.online_payment_for_order', ['order_id' => $order->id]),
            ]);

            // Create Paymob order
            $paymobOrder = $this->createPaymobOrder($transaction, $order);

            // Update transaction with Paymob reference
            $transaction->update([
                'payment_reference' => $paymobOrder['id'] ?? null,
                'metadata' => [
                    'paymob_order_id' => $paymobOrder['id'] ?? null,
                    'paymob_order_data' => $paymobOrder,
                ],
            ]);

            return $transaction->fresh();
        } catch (Exception $e) {
            Log::error('Paymob Payment Error', [
                'order_id' => $order->id,
                'amount' => $amount,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Create order in Paymob
     *
     * @param Transaction $transaction
     * @param Order $order
     * @return array
     * @throws Exception
     */
    protected function createPaymobOrder(Transaction $transaction, Order $order): array
    {
        try {
            $token = $this->authenticate();

            $user = $order->user;

            $response = Http::post($this->baseUrl . '/api/ecommerce/orders', [
                'auth_token' => $token,
                'amount_cents' => (int)($transaction->amount * 100),
                'currency' => $this->currency,
                'shipping_data' => [
                    'first_name' => $user->name ?? 'N/A',
                    'last_name' => $user->name ?? 'N/A',
                    'email' => $user->email ?? '',
                    'phone_number' => $user->phone ?? '',
                ],
                'integrations' => $this->integrationId,
            ]);

           
            if (!$response->successful()) {
                $error = $response->json();
                Log::error('Paymob Create Order Error', [
                    'status' => $response->status(),
                    'response' => $error,
                ]);
                throw new Exception('Paymob Create Order Failed: ' . ($error['detail'] ?? $error['message'] ?? 'Unknown error'));
            }

            return $response->json();
        } catch (Exception $e) {
            Log::error('Paymob Create Order Exception', [
                'message' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
