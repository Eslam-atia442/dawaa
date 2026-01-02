<?php

namespace App\Models;

use App\Traits\HasWalletTraitTrait;
use App\Traits\ModelTrait;
use App\Traits\SearchTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Spatie\MediaLibrary\HasMedia;
use App\Traits\HasMediaConversionsTrait;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Translatable\HasTranslations;

class Admin extends Authenticatable implements HasMedia
{
    use SoftDeletes, ModelTrait,
        Notifiable,
        SearchTrait, HasTranslations,
        HasFactory, HasRoles,
        HasWalletTraitTrait, HasMediaConversionsTrait;

    protected       $fillable            = ['name', 'email', 'password', 'is_active', 'is_blocked'];
    protected array $filters             = ['keyword', 'createdAtMin', 'createdAtMax', 'isBlocked', 'isActive'];
    protected array $searchable          = ['name', 'email'];
    protected array $dates               = [];
    public array    $translatable        = [];
    public array    $restrictedRelations = [];
    public array    $cascadedRelations   = [];
    public array    $filesToUpload       = ['profile'];
    public const ADDITIONAL_PERMISSIONS = [];
    public const DISABLE_PERMISSIONS    = false;
    public const DISABLE_LOG            = false;

    //--------------------- casting  -------------------------------------

    public function setPasswordAttribute($input): void{
        if ($input) {
            $this->attributes['password'] = app('hash')->needsRehash($input) ? Hash::make($input) : $input;
        }
    }

    //--------------------- relations -------------------------------------

    //--------------------- functions -------------------------------------

    //--------------------- scopes -------------------------------------

    public function scopeOfIsBlocked($query, $value){
        return $query->where('is_blocked', $value);
    }

    public function scopeOfIsActive($query, $value){
        return $query->where('is_active', $value);
    }
}
