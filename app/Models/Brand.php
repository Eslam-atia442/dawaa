<?php

namespace App\Models;

use App\Traits\HasMediaConversionsTrait;
use App\Traits\ModelTrait;
use App\Traits\SearchTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Translatable\HasTranslations;

class Brand extends Model implements HasMedia
{
    use SoftDeletes, ModelTrait, SearchTrait, HasTranslations, HasFactory, HasMediaConversionsTrait;

    protected $guarded = [];
    protected array $filters = ['keyword', 'createdAtMin', 'createdAtMax'];
    protected array $searchable = ['name'];
    protected array $dates = [];
    public array $translatable = ['name'];
    public array $restrictedRelations = [];
    public array $cascadedRelations = [];
    public array $filesToUpload = ['image'];
    public const ADDITIONAL_PERMISSIONS = [];
    public const DISABLE_PERMISSIONS    = false;
    public const DISABLE_LOG            = false;

    //--------------------- casting  -------------------------------------

    //--------------------- relations -------------------------------------

    //--------------------- functions -------------------------------------

    //--------------------- scopes -------------------------------------

   public function scopeOfActive($query)
    {
        return $query->where('is_active', 1);
    }

}
