<?php

namespace App\Http\Resources;


use \Illuminate\Http\Request;

class ChildProductResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request) : array
    {
        $this->micro = [
            'id' => $this->id,
            'name' => $this->name,
        ];

        $this->mini = [
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'has_discount' => $this->has_discount,
            'discount_percentage' => $this->discount_percentage,
            'discounted_price' => $this->discounted_price,
            'image' => $this->getFirstMediaUrl('image'),
            'expiry_date' => $this->expiry_date?->format('Y-m-d H:i:s'),
            'production_line_number' => $this->production_line_number,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
        $this->full = [
            'is_active' => $this->is_active,
            'has_discount' => $this->has_discount,
            'discount_percentage' => $this->discount_percentage,
            'discounted_price' => $this->discounted_price,
        ];
        //$this->relationLoaded()
        $this->relations = [
            'parent' =>  $this->relationLoaded('parent') ? new ProductResource($this->parent) : null,
        ];
        return $this->getResource();
    }
}