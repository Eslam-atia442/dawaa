<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductQuantityHistory;
use Exception;
use Illuminate\Support\Facades\DB;

class ProductQuantityService
{
    /**
     * Credit quantity to product (increase quantity).
     *
     * @throws Exception
     */
    public function credit(
        Product $product,
        int $quantity,
        string $reason = 'buy',
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $notes = null
    ): ProductQuantityHistory {
        if ($quantity <= 0) {
            throw new Exception(__('trans.quantity_must_be_positive'));
        }

        return DB::transaction(function () use ($product, $quantity, $reason, $referenceType, $referenceId, $notes) {
            $quantityBefore = $product->quantity;
            $quantityAfter = $quantityBefore + $quantity;

            $product->update(['quantity' => $quantityAfter]);

            return ProductQuantityHistory::create([
                'product_id' => $product->id,
                'quantity_change' => $quantity,
                'quantity_before' => $quantityBefore,
                'quantity_after' => $quantityAfter,
                'type' => 'credit',
                'reason' => $reason,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'notes' => $notes,
                'admin_id' => auth()->guard('admin')->id(),
            ]);
        });
    }

    /**
     * Debit quantity from product (decrease quantity).
     *
     * @throws Exception
     */
    public function debit(
        Product $product,
        int $quantity,
        string $reason = 'order',
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $notes = null
    ): ProductQuantityHistory {
        if ($quantity <= 0) {
            throw new Exception(__('trans.quantity_must_be_positive'));
        }

        return DB::transaction(function () use ($product, $quantity, $reason, $referenceType, $referenceId, $notes) {
            $quantityBefore = $product->quantity;

            if ($quantityBefore < $quantity) {
                throw new Exception(__('trans.insufficient_product_quantity'));
            }

            $quantityAfter = $quantityBefore - $quantity;

            $product->update(['quantity' => $quantityAfter]);

            return ProductQuantityHistory::create([
                'product_id' => $product->id,
                'quantity_change' => -$quantity, // negative for debit
                'quantity_before' => $quantityBefore,
                'quantity_after' => $quantityAfter,
                'type' => 'debit',
                'reason' => $reason,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'notes' => $notes,
                'admin_id' => auth()->guard('admin')->id(),
            ]);
        });
    }

    /**
     * Refund quantity to product (wrapper around credit with refund context).
     *
     * @throws Exception
     */
    public function refund(
        Product $product,
        int $quantity,
        ?string $referenceType = 'refund',
        ?int $referenceId = null,
        ?string $notes = null
    ): ProductQuantityHistory {
        $notes = $notes ?? __('trans.quantity_refund');

        return $this->credit(
            $product,
            $quantity,
            'refund',
            $referenceType,
            $referenceId,
            $notes
        );
    }

    /**
     * Get quantity history for a product.
     */
    public function getHistory(Product $product, array $filters = [])
    {
        $query = ProductQuantityHistory::where('product_id', $product->id)
            ->with(['admin'])
            ->latest();

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['reason'])) {
            $query->where('reason', $filters['reason']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->paginate($filters['per_page'] ?? 15);
    }
}