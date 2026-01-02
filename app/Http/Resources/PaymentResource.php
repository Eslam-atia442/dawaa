<?php

namespace App\Http\Resources;


use \Illuminate\Http\Request;

class PaymentResource extends BaseResource
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
            'payment_id' => $this->payment_id,
            'trace_id' => $this->trace_id,
            'amount' => $this->amount,
        ];

        $this->full = [
            'status_text' => $this->status?->value,
            'invoice_id' => $this->invoice_id,
            'currency' => $this->currency,
            'comment' => $this->comment,
            'paid_at' => $this->paid_at,
            'data' => $this->data,
            'user_id' => $this->user_id
        ];

        //$this->relationLoaded()
        $this->relations = [
            'user'=>  $this->relationLoaded('user') ? new UserResource($this->user) : null
        ];
        return $this->getResource();
    }
}
