<?php

namespace App\Http\Resources;


use \Illuminate\Http\Request;

class IntroResource extends BaseResource
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
            'title' => $this->name,
            'description' => $this->description,
            'image' => $this->getFirstMediaUrl('image'),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
        $this->full = [
            'is_active' => $this->is_active,
            'image' => $this->getFirstMediaUrl('image'),
            'title' => $this->name,
            'description' => $this->description,
        ];
        //$this->relationLoaded()
        $this->relations = [
        ];
        return $this->getResource();
    }
}
