<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\SearchTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Region extends Model
{
    use SoftDeletes, ModelTrait, SearchTrait, HasTranslations, HasFactory;

    protected $guarded = [];
    protected array $filters = ['keyword', 'createdAtMin', 'createdAtMax', 'country'];
    protected array $searchable = ['name'];
    protected array $dates = [];
    public array $filterModels = [];
    public array $filterCustom = ['country'];
    public array $translatable = ['name'];
    public array $restrictedRelations = [];
    public array $cascadedRelations = [];
    public array $filesToUpload = [];
    public const ADDITIONAL_PERMISSIONS = [];
    public const DISABLE_PERMISSIONS    = false;
    public const DISABLE_LOG            = false;

    //--------------------- casting  -------------------------------------

    //--------------------- relations -------------------------------------

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    //--------------------- functions -------------------------------------

    //--------------------- scopes -------------------------------------
    public function scopeOfCountry($query, $country)
    {
        return $query->whereIn('country_id', (array)$country);
    }

    public function scopeOfActive($query)
    {
        return $query->whereHas('country', function ($query){
            $query->where('is_active', 1);
        });
    }

}
