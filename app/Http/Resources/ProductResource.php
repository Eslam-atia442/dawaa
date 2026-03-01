<?php

namespace App\Http\Resources;


use \Illuminate\Http\Request;

class ProductResource extends BaseResource
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
        ];
        $this->mini = [
            'name' => $this->name,
            'description' => $this->description,
            
            'image' => $this->getFirstMediaUrl('image'),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
        $this->full = [
            'is_active' => $this->is_active,
            'has_discount' => $this->has_discount ?? false,
            'discount_percentage' => $this->has_discount ? $this->discount_percentage : null,
            'discounted_price' => $this->discounted_price,
            
            'gallery' => $this->getMedia('gallery')->map(function ($media) {
                return [
                    'id' => $media->id,
                    'url' => $media->getUrl(),
                ];
            })->toArray(),
        ];
        //$this->relationLoaded()
        $this->relations = [
            'store' =>  $this->relationLoaded('store') ? new StoreResource($this->store) : null,
            'category' =>  $this->relationLoaded('category') ? new CategoryResource($this->category) : null,
            'brand' =>  $this->relationLoaded('brand') ? new BrandResource($this->brand) : null,
            'oldest_child_product' =>  $this->relationLoaded('oldestChildProduct') ? new ChildProductResource($this->oldestChildProduct) : null,
        ];
        return $this->getResource();
    }
}
