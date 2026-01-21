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
            'active_class' => $this->active_class,
            'active_status' => $this->active_status
        ];
        //$this->relationLoaded()
        $this->relations = [
            'store' =>  $this->relationLoaded('store') ? new StoreResource($this->store) : null,
            'category' =>  $this->relationLoaded('category') ? new CategoryResource($this->category) : null,
            'brand' =>  $this->relationLoaded('brand') ? new BrandResource($this->brand) : null,
            'city' =>  $this->relationLoaded('city') ? new CityResource($this->city) : null,
        ];
        return $this->getResource();
    }
}
