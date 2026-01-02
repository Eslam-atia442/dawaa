<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\SearchTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class City extends Model
{
    use SoftDeletes, ModelTrait, SearchTrait, HasTranslations, HasFactory;

    protected $fillable = ['name', 'region_id'];
    protected array $filters = ['keyword', 'createdAtMin', 'createdAtMax', 'region'];
    protected array $searchable = ['name'];
    protected array $dates = [];
    public array $translatable = ['name'];
    public array $restrictedRelations = [];
    public array $cascadedRelations = [];
    public array $filesToUpload = [];
    public const ADDITIONAL_PERMISSIONS = [];
    public const DISABLE_PERMISSIONS    = false;
    public const DISABLE_LOG = false;

    //--------------------- casting  -------------------------------------

    //--------------------- relations -------------------------------------

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    //--------------------- functions -------------------------------------

    //--------------------- scopes -------------------------------------

    public function scopeOfRegion($query, $region)
    {
        return $query->whereIn('region_id', (array)$region);
    }
}
