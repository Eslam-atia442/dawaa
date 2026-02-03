<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class OrderItemResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        $this->micro = [
            'id' => $this->id,
            'quantity' => $this->quantity,
            'price' => (float) ($this->discounted_price ?? $this->price),
            'total_price' => (float) $this->total_price,
        ];
        $this->mini = [
            'id' => $this->id,
            'quantity' => $this->quantity,
            'price' => (float) ($this->discounted_price ?? $this->price),
            'total_price' => (float) $this->total_price,
            'has_discount' => ($this->discount_amount ?? 0) > 0,
            'discount_amount' => (float) ($this->discount_amount ?? 0),
            'discount_percentage' => $this->discount_percentage,
            'original_price' => (float) ($this->original_price ?? $this->price),
            'original_total_price' => (float) ($this->original_total_price ?? $this->total_price),
            'total_discount' => (float) ($this->total_discount ?? 0),
            'note' => $this->note,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
        $this->full = [
            'price' => (float) ($this->discounted_price ?? $this->price),
            'original_price' => (float) ($this->original_price ?? $this->price),
            'discount_amount' => (float) ($this->discount_amount ?? 0),
            'discounted_price' => (float) ($this->discounted_price ?? $this->price),
            'discount_percentage' => $this->discount_percentage,
            'total_price' => (float) $this->total_price,
            'original_total_price' => (float) ($this->original_total_price ?? $this->total_price),
            'total_discount' => (float) ($this->total_discount ?? 0),
        ];

        $this->relations = [
            'product' => $this->whenLoaded('product', function () {
                return new ProductResource($this->product);
            }),
            'child_product' => $this->whenLoaded('childProduct', function () {
                return $this->childProduct ? new ChildProductResource($this->childProduct) : null;
            }),
        ];

        return $this->getResource();
    }
}
