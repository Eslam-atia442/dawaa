<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class PermissionResource extends BaseResource
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
            'name' => $this->name,
        ];

        $this->mini = [
            'module' => $this->model,
            'action' => $this->action
        ];

        $this->full = [
        ];

        $this->relations = [

        ];

        return $this->getResource();
    }
}
