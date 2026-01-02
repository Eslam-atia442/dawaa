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
    protected array $filters = ['keyword', 'createdAtMin', 'createdAtMax'];
    protected array $searchable = ['name'];
    protected array $dates = [];
    public array $translatable = ['name'];
    public array $restrictedRelations = [];
    public array $filesToUpload
        = [
            'gallery',
            'logo'
        ];
//    public const ADDITIONAL_PERMISSIONS = ['export', 'import'];
    public const DISABLE_PERMISSIONS    = true;
    public const DISABLE_LOG            = false;

    //--------------------- casting  -------------------------------------

    //--------------------- relations -------------------------------------


    //--------------------- functions -------------------------------------

    //--------------------- scopes -------------------------------------

}
