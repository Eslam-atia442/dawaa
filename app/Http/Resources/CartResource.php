<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        $items = $this->resource['items'] ?? collect();
        $itemsCount = $this->resource['items_count'] ?? 0;
        $totalPrice = $this->resource['total_price'] ?? 0;

        return [
            'items' => CartItemResource::collection($items),
            'summary' => [
                'items_count' => $itemsCount,
                'total_price' => $totalPrice,
            ],
        ];
    }
}
