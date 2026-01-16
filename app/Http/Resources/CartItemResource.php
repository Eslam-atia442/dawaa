<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product' => $this->whenLoaded('product', function () {
                return [
                    'id' => $this->product->id,
                    'name' => $this->product->name,
                ];
            }),
            'child_product' => $this->whenLoaded('childProduct', function () {
                return $this->childProduct ? [
                    'id' => $this->childProduct->id,
                    'name' => $this->childProduct->name,
                    'price' => $this->childProduct->price,
                ] : null;
            }),
            'quantity' => $this->quantity,
            'price' => $this->price,
            'total_price' => $this->total_price,
            'note' => $this->note,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
