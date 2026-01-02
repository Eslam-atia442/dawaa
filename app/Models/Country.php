<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\SearchTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Country extends Model
{
    use SoftDeletes, ModelTrait, SearchTrait, HasTranslations, HasFactory;
    protected $guarded = ['id'];
    protected array $filters = ['keyword','createdAtMin','createdAtMax','active'];
    protected array $searchable = ['name'];
    protected array $dates = [];
    public array $translatable = ['name' ,'currency'];
    public array $restrictedRelations = ['users'];
    public array $cascadedRelations = [];
    public array $filesToUpload = [];
    public const ADDITIONAL_PERMISSIONS = ['read-all', 'read'];
    public const DISABLE_PERMISSIONS    = true;
    public const DISABLE_LOG = false;

    //--------------------- casting  -------------------------------------

    //--------------------- relations -------------------------------------

    //--------------------- functions -------------------------------------

    //--------------------- scopes -------------------------------------
    public function scopeOfActive($query)
    {
        return $query->where('is_active', 1);
    }
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
