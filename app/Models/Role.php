<?php

namespace App\Models;

use App\Traits\HasMediaConversionsTrait;
use App\Traits\ModelTrait;
use App\Traits\SearchTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;

class Role extends \Spatie\Permission\Models\Role implements HasMedia
{
    use ModelTrait, SearchTrait , HasMediaConversionsTrait;

    public const DEFAULT_ROLE_SUPER_ADMIN = 'Super Admin';
    public const DEFAULT_ROLE
                                          = [
            self::DEFAULT_ROLE_SUPER_ADMIN,
        ];

    public const ADDITIONAL_PERMISSIONS = [];
    protected       $fillable            = ['id', 'name', 'name_ar', 'guard_name', 'is_active'];
    protected array $filters             = ['keyword'];
    protected array $searchable          = ['name'];
    public array    $restrictedRelations = ['users'];
    protected       $appends             = ['translated_name'];
    public const DISABLE_LOG = false;

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getTranslatedNameAttribute(){
        if (app()->getLocale() == 'en') {
            return $this->attributes['name'];
        } else {
            return $this->name_ar;
        }
    }

    public function scopeOfActive($query){
        return $query->where('is_active', 1);
    }
}
