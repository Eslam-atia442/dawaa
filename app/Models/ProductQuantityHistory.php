<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\SearchTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductQuantityHistory extends Model
{
    use SoftDeletes, ModelTrait, SearchTrait, HasFactory;

    protected $fillable = [
        'product_id',
        'quantity_change',
        'quantity_before',
        'quantity_after',
        'type',
        'reason',
        'reference_type',
        'reference_id',
        'notes',
        'admin_id'
    ];

    protected array $filters = ['keyword', 'createdAtMin', 'createdAtMax', 'product_id', 'reason', 'type'];
    protected array $searchable = [];
    protected array $dates = [];
    public array $restrictedRelations = [];
    public array $cascadedRelations = [];
    public const ADDITIONAL_PERMISSIONS = [];
    public const DISABLE_PERMISSIONS = true;
    public const DISABLE_LOG = false;

    //--------------------- casting  -------------------------------------

    protected $casts = [
        'quantity_change' => 'integer',
        'quantity_before' => 'integer',
        'quantity_after' => 'integer',
    ];

    //--------------------- relations -------------------------------------

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    //--------------------- functions -------------------------------------

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'credit' => __('trans.quantity_credit'),
            'debit' => __('trans.quantity_debit'),
            default => $this->type
        };
    }

    public function getReasonLabelAttribute(): string
    {
        return match($this->reason) {
            'buy' => __('trans.quantity_buy'),
            'refund' => __('trans.quantity_refund'),
            'order' => __('trans.quantity_order'),
            'adjustment' => __('trans.quantity_adjustment'),
            'return' => __('trans.quantity_return'),
            default => $this->reason
        };
    }

    //--------------------- scopes -------------------------------------

    public function scopeOfProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOfReason($query, $reason)
    {
        return $query->where('reason', $reason);
    }
}
