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

class Product extends Model implements HasMedia
{
    use SoftDeletes, ModelTrait, SearchTrait, HasTranslations, HasFactory, HasMediaConversionsTrait;

    protected $guarded = [];
    protected array $filters = ['keyword', 'createdAtMin', 'createdAtMax', 'parentId' , 'parent'];
    protected array $searchable = ['name'];
    protected array $dates = ['expiry_date'];
    public array $translatable = ['name', 'description'];
    public array $restrictedRelations = [];
    public array $cascadedRelations = [];
    public array $filesToUpload = ['image'];
    public const ADDITIONAL_PERMISSIONS = [];
    public const DISABLE_PERMISSIONS    = false;
    public const DISABLE_LOG            = false;

    //--------------------- casting  -------------------------------------

    //--------------------- relations -------------------------------------

    public function parent()
    {
        return $this->belongsTo(Product::class, 'parent_id');
    }

    public function childProducts()
    {
        return $this->hasMany(Product::class, 'parent_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    //--------------------- functions -------------------------------------

    public function isParent(): bool
    {
        return is_null($this->parent_id);
    }

    public function isChild(): bool
    {
        return !is_null($this->parent_id);
    }

    //--------------------- scopes -------------------------------------

    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeChildren($query)
    {
        return $query->whereNotNull('parent_id');
    }

    public function scopeOfActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function scopeOfParentId($query, $value)
    {
        if ($value === null || $value === 'null') {
            return $query->whereNull('parent_id');
        } elseif ($value === 'not_null') {
            return $query->whereNotNull('parent_id');
        } else {
            return $query->where('parent_id', $value);
        }
    }

    public function scopeOfParent($query)
    {
        return $query->whereNull('parent_id');
    }

}
