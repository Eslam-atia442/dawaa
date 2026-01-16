<?php

namespace App\Models;

use App\Enums\TransactionStatusEnum;
use App\Enums\TransactionTypeEnum;
use App\Traits\HasMediaConversionsTrait;
use App\Traits\ModelTrait;
use App\Traits\SearchTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Translatable\HasTranslations;

class Transaction extends Model implements HasMedia
{
    use SoftDeletes, ModelTrait, SearchTrait, HasTranslations, HasFactory, HasMediaConversionsTrait;

    protected $guarded = [];
    protected array $filters = ['keyword', 'createdAtMin', 'createdAtMax', 'type', 'status'];
    protected array $searchable = ['transaction_reference', 'payment_reference', 'description'];
    protected array $dates = ['processed_at'];
    public array $translatable = [];
    public array $restrictedRelations = [];
    public array $cascadedRelations = [];
    public array $filesToUpload = [];
    public const ADDITIONAL_PERMISSIONS = [];
    public const DISABLE_PERMISSIONS = false;
    public const DISABLE_LOG = false;

    //--------------------- casting  -------------------------------------
    protected $casts = [
        'type' => TransactionTypeEnum::class,
        'status' => TransactionStatusEnum::class,
        'payment_method' => \App\Enums\PaymentTypeEnum::class,
        'metadata' => 'array',
        'processed_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    //--------------------- relations -------------------------------------
    /**
     * Get the parent transactionable model (Order, Wallet, etc.)
     */
    public function transactionable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the model that initiated the transaction (User, Admin, etc.)
     */
    public function initiatedBy(): MorphTo
    {
        return $this->morphTo('initiated_by');
    }

    /**
     * Get the model that processed the transaction (Admin, System, etc.)
     */
    public function processedBy(): MorphTo
    {
        return $this->morphTo('processed_by');
    }

    //--------------------- functions -------------------------------------
    
    /**
     * Generate a unique transaction reference
     */
    public static function generateReference(): string
    {
        do {
            $reference = 'TXN-' . strtoupper(uniqid()) . '-' . time();
        } while (self::where('transaction_reference', $reference)->exists());
        
        return $reference;
    }
    
    /**
     * Mark transaction as completed
     */
    public function markAsCompleted(): bool
    {
        return $this->update([
            'status' => TransactionStatusEnum::COMPLETED,
            'processed_at' => now(),
        ]);
    }
    
    /**
     * Mark transaction as failed
     */
    public function markAsFailed(string $reason = null): bool
    {
        $data = [
            'status' => TransactionStatusEnum::FAILED,
            'processed_at' => now(),
        ];
        
        if ($reason) {
            $data['notes'] = ($this->notes ? $this->notes . "\n" : '') . "Failed: {$reason}";
        }
        
        return $this->update($data);
    }
    
    /**
     * Mark transaction as cancelled
     */
    public function markAsCancelled(string $reason = null): bool
    {
        $data = [
            'status' => TransactionStatusEnum::CANCELLED,
            'processed_at' => now(),
        ];
        
        if ($reason) {
            $data['notes'] = ($this->notes ? $this->notes . "\n" : '') . "Cancelled: {$reason}";
        }
        
        return $this->update($data);
    }

    //--------------------- scopes -------------------------------------
    
    /**
     * Scope to filter by transaction type
     */
    public function scopeOfType($query, TransactionTypeEnum $type)
    {
        return $query->where('type', $type->value);
    }
    
    /**
     * Scope to filter by transaction status
     */
    public function scopeOfStatus($query, TransactionStatusEnum $status)
    {
        return $query->where('status', $status->value);
    }
    
    /**
     * Scope to get completed transactions
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', TransactionStatusEnum::COMPLETED->value);
    }
    
    /**
     * Scope to get pending transactions
     */
    public function scopePending($query)
    {
        return $query->where('status', TransactionStatusEnum::PENDING->value);
    }
    
    /**
     * Scope to get failed transactions
     */
    public function scopeFailed($query)
    {
        return $query->where('status', TransactionStatusEnum::FAILED->value);
    }
    
    /**
     * Scope to get transactions initiated by a specific model
     */
    public function scopeInitiatedBy($query, $modelType, $modelId)
    {
        return $query->where('initiated_by_type', $modelType)
                     ->where('initiated_by_id', $modelId);
    }
    
    /**
     * Scope to get transactions processed by a specific model
     */
    public function scopeProcessedBy($query, $modelType, $modelId)
    {
        return $query->where('processed_by_type', $modelType)
                     ->where('processed_by_id', $modelId);
    }
    
    /**
     * Scope to get transactions for a specific model
     */
    public function scopeForModel($query, $modelType, $modelId)
    {
        return $query->where('transactionable_type', $modelType)
                     ->where('transactionable_id', $modelId);
    }
    
    /**
     * Scope to get transactions within a date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function scopeOfActive($query)
    {
        return $query->where('status', TransactionStatusEnum::COMPLETED->value);
    }
}
