<?php

namespace App\Http\Resources;


use \Illuminate\Http\Request;

class SliderResource extends BaseResource
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
        ];
        $this->mini = [
            'name' => $this->name,
            'image' => $this->getFirstMediaUrl('image'),
        ];
        $this->full = [
            'is_active' => $this->is_active,
        ];
        //$this->relationLoaded()
        $this->relations = [];
        return $this->getResource();
    }
}
