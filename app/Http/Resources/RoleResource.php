<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class RoleResource extends BaseResource
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

        $this->mini = [];

        $this->full = [
            'can_be_deleted' => $this->can_be_deleted,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at->format(config('app.date_format')),
        ];

        $this->relations = [
            'permissions' => $this->relationLoaded('permissions') ? PermissionResource::collection($this->permissions) : null,
            'role_permissions' => $this->relationLoaded('permissions') ? $this->customizePermissions()  : null,
        ];

        return $this->getResource();
    }
    private function customizePermissions()
    {
        return $this->permissions->groupBy('model')->map(function ($model, $key) {
            return $model->pluck('id');
        })->collect();
    }
}
