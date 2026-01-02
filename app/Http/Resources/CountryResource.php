<?php

namespace App\Http\Resources;


use \Illuminate\Http\Request;

class CountryResource extends BaseResource
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
        $this->mini  = [
            'flag'         => 'https://flagsapi.com/' . $this->iso2 . '/shiny/64.png',
            'name'         => $this->name,
            'country_code' => '+' . $this->key,
        ];
        $this->full  = [
        ];
        //$this->relationLoaded()
        $this->relations = [
        ];
        return $this->getResource();
    }
}
