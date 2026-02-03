<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class WalletResource extends BaseResource
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
            'id' => $this->id,
            'balance' => (float) $this->balance,
            'status' => (bool) $this->status,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
        
        $this->full = [
            'balance' => (float) $this->balance,
            'status' => (bool) $this->status,
            'status_label' => $this->status ? __('trans.active') : __('trans.inactive'),
        ];
        
        return $this->getResource();
    }
}
