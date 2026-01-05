<?php

namespace App\Http\Resources;


use App\Enums\GenderEnum;
use App\Enums\UserTypeEnum;
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
            'type'                 => $this->type?->value,
            'type_label'           => $this->type?->label(),
            'email'                => $this->email,
            'register_by_social'   => (boolean)$this->social_type ?? false,
            'social_type_provider' => $this->social_type ?? null,
            'phone'                => $this->phone,
            'gender'               => $this->gender ? GenderEnum::from($this->gender)->label() : null,
            'dob'                  => $this->dob,
            'country_id'           => $this->country_id,
            'created_at'           => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at'           => $this->updated_at?->format('Y-m-d H:i:s'),
            'license'              => $this->getFirstMediaUrl('license'),
            'tax_card'             => $this->getFirstMediaUrl('tax_card'),
            'front_card_image'     => $this->getFirstMediaUrl('front_card_image'),
            'back_card_image'      => $this->getFirstMediaUrl('back_card_image'),
            'phone_verified_at'    => $this->phone_verified_at,
            'is_accepted'          => $this->is_accepted,
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
