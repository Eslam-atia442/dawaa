<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\SearchTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use App\Traits\HasMediaConversionsTrait;
use Spatie\Translatable\HasTranslations;

class Category extends Model implements HasMedia
{
    use  ModelTrait, SearchTrait, HasTranslations, HasFactory, HasMediaConversionsTrait;

    protected $guarded = ['id'];
    protected array $filters = ['keyword', 'createdAtMin', 'createdAtMax', 'active'];
    protected array $searchable = ['name'];
    protected array $dates = [];
    public array $translatable = ['name'];
    public array $restrictedRelations = [];
    public array $filesToUpload
        = [
            'image'
        ];
//    public const ADDITIONAL_PERMISSIONS = ['export', 'import'];
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
