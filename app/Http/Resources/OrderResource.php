<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\OrderItemResource;
use App\Http\Resources\UserResource;

class OrderResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        $subtotal = $this->relationLoaded('items') ? $this->subtotal : (float) $this->total_price;
        $totalDiscount = $this->relationLoaded('items') ? $this->total_discount : 0;
        
        $this->micro = [
            'id' => $this->id,
            'total_price' => (float) $this->total_price,
        ];
        $this->mini = [
            'id' => $this->id,
            'subtotal' => $subtotal,
            'total_discount' => $totalDiscount,
            'total_price' => (float) $this->total_price,
            'payment_type' => $this->payment_type?->value,
            'payment_type_label' => $this->payment_type?->label(),
            'status' => $this->status?->value,
            'status_label' => $this->status?->label(),
            'items_count' => $this->items_count ?? $this->whenLoaded('items', fn() => $this->items->count()),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
        $this->full = [
            'subtotal' => $subtotal,
            'total_discount' => $totalDiscount,
            'total_price' => (float) $this->total_price,
            'refund_type' => $this->refund_type?->value,
            'refund_type_label' => $this->refund_type?->label(),
            'note' => $this->note,
        ];

        $this->relations = [

            'items' => OrderItemResource::collection($this->whenLoaded('items')),
        ];

        return $this->getResource();
    }
}
