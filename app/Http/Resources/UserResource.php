<?php

namespace App\Http\Resources;


use App\Enums\GenderEnum;
use \Illuminate\Http\Request;

class UserResource extends BaseResource
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
            'name'                 => $this->name,
            'email'                => $this->email,
            'register_by_social'   => (boolean)$this->social_type ?? false,
            'social_type_provider' => $this->social_type ?? null,
            'phone'                => $this->phone,
            'gender'               => GenderEnum::from($this->gender)->label(),
            'dob'                  => $this->dob,
            'country_id'           => $this->country_id,
            'created_at'           => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at'           => $this->updated_at?->format('Y-m-d H:i:s'),
            'avatar'               => $this->getFirstMediaUrl('avatar'),
            'phone_verified_at'    => $this->phone_verified_at,
        ];
        $this->full  = [
            'is_active'   => $this->is_active,
            'accessToken' => $this->accessToken
        ];
        //$this->relationLoaded()
        $this->relations = [];
        return $this->getResource();
    }
}
