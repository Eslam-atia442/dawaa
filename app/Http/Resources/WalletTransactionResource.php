<?php

namespace App\Http\Resources;

use App\Enums\WalletTransactionTypeEnum;
use Illuminate\Http\Request;

class WalletTransactionResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        $typeEnum = WalletTransactionTypeEnum::tryFrom($this->type);
        $typeLabel = $typeEnum ? $typeEnum->label() : __('trans.unknown');
        
        $this->micro = [
            'id' => $this->id,
        ];
        
        $this->mini = [
            'id' => $this->id,
            'amount' => (float) $this->amount,
            'type' => $this->type,
            'type_label' => $typeLabel,
            'balance_before' => (float) $this->balance_before,
            'balance_after' => (float) $this->balance_after,
            'description' => $this->description,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
        
        $this->full = [
            'amount' => (float) $this->amount,
            'type' => $this->type,
            'type_label' => $typeLabel,
            'balance_before' => (float) $this->balance_before,
            'balance_after' => (float) $this->balance_after,
            'reference_type' => $this->reference_type,
            'reference_id' => $this->reference_id,
            'description' => $this->description,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
        
        $this->relations = [
            // 'wallet' => $this->relationLoaded('wallet') ? new WalletResource($this->wallet) : null,
        ];
        
        return $this->getResource();
    }
}
