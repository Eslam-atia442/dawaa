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

class Setting extends Model implements HasMedia
{
    use   ModelTrait, SearchTrait, HasTranslations, HasFactory, HasMediaConversionsTrait;

    protected $guarded = ['id'];
    protected array $filters = ['keyword', 'createdAtMin', 'createdAtMax'];
    protected array $searchable = [];
    protected array $dates = [];
    public array $translatable = [];
    public array $restrictedRelations = [];
    public array $cascadedRelations = [];
    public array $filesToUpload
        = [
            'logo_ar',
            'logo_en',
            'fav_icon',
            'default_user',
            'no_data_icon',
            'login_background',
        ];
    public const ADDITIONAL_PERMISSIONS = ['read-all', 'update', 'fcm'];
    public const DISABLE_PERMISSIONS    = true;
    public const DISABLE_LOG            = false;

    //--------------------- casting  -------------------------------------

    public function getValueAttribute()
    {
        $files = $this->filesToUpload;

        foreach ($files as $file) {
            if ($this->attributes['value'] == $file) {
                $this->attributes['value'] = $this->getFirstMediaUrl($file);
            }
        }
        return $this->attributes['value'];

    }
    //--------------------- relations -------------------------------------

    //--------------------- functions -------------------------------------

    //--------------------- scopes -------------------------------------

}
