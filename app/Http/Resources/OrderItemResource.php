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
            'price' => $this->price,
            'total_price' => $this->total_price,
        ];
        $this->mini = [
            'id' => $this->id,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'total_price' => $this->total_price,
            'note' => $this->note,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
        $this->full = [];

        $this->relations = [
            'product' => $this->whenLoaded('product', function () {
                return new ProductResource($this->product);
            }),
            'child_product' => $this->whenLoaded('childProduct', function () {
                return new ProductResource($this->childProduct);
            }),
        ];

        return $this->getResource();
    }
}
