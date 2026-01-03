<?php

namespace App\Http\Resources;


use \Illuminate\Http\Request;

class BrandResource extends BaseResource
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
            'id' => $this->id,
            'name' => $this->name,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
        $this->full = [
            'image' => $this->getfirstMediaUrl('image'),
            'is_active' => $this->is_active,
        ];
        //$this->relationLoaded()
        $this->relations = [
        ];
        return $this->getResource();
    }
}
