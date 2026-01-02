<?php

namespace App\Http\Resources;


use \Illuminate\Http\Request;

class SettingResource extends BaseResource
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
           'key' => $this->key,
           'value' => $this->value
        ];
        $this->mini = [
        ];
        $this->full = [
        ];
        //$this->relationLoaded()
        $this->relations = [
        ];
        return $this->getResource();
    }
}
