<?php

namespace App\Http\Resources;


use \Illuminate\Http\Request;

class CategoryResource extends BaseResource
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
            'id'   => $this->id,
            'name' => $this->name,
        ];

        $this->full = [
            'image' => $this->getfirstMediaUrl('image'),
        ];

        //$this->relationLoaded()
        $this->relations = [
        ];
        return $this->getResource();
    }
}
