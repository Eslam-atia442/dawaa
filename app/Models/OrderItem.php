<?php

namespace App\Models;

use App\Traits\HasMediaConversionsTrait;
use App\Traits\ModelTrait;
use App\Traits\SearchTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Translatable\HasTranslations;

class OrderItem extends Model implements HasMedia
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
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function childProduct()
    {
        return $this->belongsTo(Product::class, 'child_product_id');
    }

    //--------------------- functions -------------------------------------
    
    /**
     * Scope to get cart items (items without order_id)
     */
    public function scopeCartItems($query, $userId = null)
    {
        $query = $query->whereNull('order_id');
        
        if ($userId) {
            $query->where('user_id', $userId);
        }
        
        return $query;
    }
    
    /**
     * Scope to get order items (items with order_id)
     */
    public function scopeOrderItems($query, $orderId = null)
    {
        $query = $query->whereNotNull('order_id');
        
        if ($orderId) {
            $query->where('order_id', $orderId);
        }
        
        return $query;
    }

    //--------------------- scopes -------------------------------------

   public function scopeOfActive($query)
    {
        return $query->where('is_active', 1);
    }

}
