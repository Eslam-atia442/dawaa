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

    protected $guarded = ['id'];
    protected array $filters = [
        'keyword',
        'createdAtMin',
        'createdAtMax',
        'parentId',
        'parent',
        'store',
        'city',
        'category',
        'brand',
        'hasChildren',
        'fromPrice',
        'toPrice',
        'recentlyAdded',
        'hasDiscount',
        'active',
    ];
    protected array $searchable = ['name'];
    protected array $dates = ['expiry_date'];
    public array $translatable = ['name', 'description'];
    public array $restrictedRelations = [];
    public array $cascadedRelations = [];
    public array $filesToUpload = ['image', 'gallery'];
    public const ADDITIONAL_PERMISSIONS = [];
    public const DISABLE_PERMISSIONS    = false;
    public const DISABLE_LOG            = false;

    //--------------------- casting  -------------------------------------

    protected $casts = [
        'expiry_date' => 'date',
        'has_discount' => 'boolean',
        'discount_percentage' => 'decimal:2',
    ];

    //--------------------- relations -------------------------------------

    public function parent()
    {
        return $this->belongsTo(Product::class, 'parent_id');
    }

    public function childProducts()
    {
        return $this->hasMany(Product::class, 'parent_id');
    }

    public function oldestChildProduct()
    {
        return $this->hasOne(Product::class, 'parent_id')->where('is_active', 1)->where('quantity', '>', 0)->orderBy('id', 'asc');
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

    //--------------------- accessors -------------------------------------

    /**
     * Get the discount status (inherited from parent if child product).
     *
     * @return bool
     */
    public function getHasDiscountAttribute(): bool
    {
        if ($this->isChild() && $this->relationLoaded('parent') && $this->parent) {
            return (bool) ($this->parent->attributes['has_discount'] ?? false);
        }
        return (bool) ($this->attributes['has_discount'] ?? false);
    }

    /**
     * Get the discount percentage (inherited from parent if child product).
     *
     * @return float|null
     */
    public function getDiscountPercentageAttribute(): ?float
    {
        if ($this->isChild() && $this->relationLoaded('parent') && $this->parent) {
            $parentHasDiscount = (bool) ($this->parent->attributes['has_discount'] ?? false);
            if ($parentHasDiscount) {
                return isset($this->parent->attributes['discount_percentage'])
                    ? (float) $this->parent->attributes['discount_percentage']
                    : null;
            }
            return null;
        }
        return isset($this->attributes['discount_percentage']) ? (float) $this->attributes['discount_percentage'] : null;
    }

    /**
     * Calculate the discounted price based on discount percentage.
     *
     * @return float|null
     */
    public function getDiscountedPriceAttribute(): ?float
    {

        if ($this->attributes['parent_id']) {

            $hasDiscount = (float) $this->parent->attributes['has_discount'];
            $discountPercentage = (float) $this->parent->attributes['discount_percentage'];
            $price = (float) $this->attributes['price'];


            if (!$hasDiscount || !$discountPercentage || !$price) {
                return null;
            }

            return $price - ($price * ($discountPercentage / 100));
        } else {
            return null;
        }
    }

   
    public function getTotalQuantityAttribute(): ?int
    {
        return $this->childProducts()->sum('quantity');
    }

    public function getIsLowQuantityAttribute(): ?bool
    {
        return $this->total_quantity < $this->minimum_quantity;
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

    public function scopeOfParent($query, $value)
    {
        if ($value === null || $value === 'null' || $value === true) {
            return $query->whereNull('parent_id');
        } elseif ($value === 'not_null') {
            return $query->whereNotNull('parent_id');
        } else {
            return $query->where('parent_id', $value);
        }
    }


    public function scopeOfStore($query, $value)
    {
        return $query->where('store_id', $value);
    }
    public function scopeOfCity($query, $value)
    {
        return $query->where('city_id', $value);
    }
    public function scopeOfCategory($query, $value)
    {
        return $query->where('category_id', $value);
    }
    public function scopeOfBrand($query, $value)
    {
        return $query->where('brand_id', $value);
    }

    public function scopeOfHasChildren($query)
    {
        return $query->whereHas('childProducts', function ($query) {
            $query->where('is_active', 1)->where('quantity', '>', 0);
        });
    }

    public function scopeOfFromPrice($query, $value)
    {
        return $query->whereHas('childProducts', function ($query) use ($value) {
            $query->where('price', '>=', $value);
        });
    }
    public function scopeOfToPrice($query, $value)
    {
        return $query->whereHas('childProducts', function ($query) use ($value) {
            $query->where('price', '<=', $value);
        });
    }
    public function scopeOfRecentlyAdded($query)
    {
        if (request()->has('recentlyAdded') && request()->recentlyAdded == true) {
            return $query->where('is_recently_added', 1);
        } else {
            return $query->where('is_recently_added', 0);
        }
    }
    public function scopeOfHasDiscount($query)
    {
        if (request()->has('hasDiscount') && request()->hasDiscount == true) {
            return $query->where('has_discount', true);
        } else {
            return $query->where('has_discount', false);
        }
    }

}
