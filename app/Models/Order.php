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

class Order extends Model implements HasMedia
{
    use SoftDeletes, ModelTrait, SearchTrait, HasTranslations, HasFactory, HasMediaConversionsTrait;

    protected $guarded = [];
    protected $fillable = [
        'user_id',
        'total_price',
        'payment_type',
        'parent_id',
        'refund_type',
        'note',
        'status',
    ];
    protected array $filters = ['keyword', 'createdAtMin', 'createdAtMax', 'myOrders', 'user'];
    protected array $searchable = ['name'];
    protected array $dates = [];
    public array $translatable = ['name'];
    public array $restrictedRelations = [];
    public array $cascadedRelations = [];
    public array $filesToUpload = ['image'];
    public const ADDITIONAL_PERMISSIONS = ['read-all', 'read','approve-refund'];
    public const DISABLE_PERMISSIONS    = true;
    public const DISABLE_LOG            = false;

    protected $casts = [
        'payment_type' => \App\Enums\PaymentTypeEnum::class,
        'refund_type' => \App\Enums\RefundTypeEnum::class,
        'status' => \App\Enums\OrderStatusEnum::class,
    ];
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parentOrder()
    {
        return $this->belongsTo(Order::class, 'parent_id');
    }

    public function refundOrders()
    {
        return $this->hasMany(Order::class, 'parent_id');
    }

    public function transactions()
    {
        return $this->morphMany(Transaction::class, 'transactionable');
    }

    public function scopeOfActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function scopeOfMyOrders($query)
    {
        if (auth('sanctum')->check()) {
            return $query->where('user_id', auth('sanctum')->id());
        }
    }

    public function scopeOfUser($query, $data)
    {
        return $query->whereIn('user_id', (array)$data);
    }
}
