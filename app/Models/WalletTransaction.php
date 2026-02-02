<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\SearchTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class WalletTransaction extends Model
{
    use SoftDeletes, ModelTrait, SearchTrait, HasTranslations, HasFactory;

    protected $fillable = [
        'wallet_id',
        'amount',
        'type',
        'balance_before',
        'balance_after',
        'reference_type',
        'reference_id',
        'description',
        'admin_id',
    ];
    protected array $filters = ['keyword', 'createdAtMin', 'createdAtMax', 'type', 'wallet', 'admin', 'reference_type', 'reference_id'];
    protected array $searchable = ['description'];
    protected array $dates = [];
    public array $translatable = [];
    public array $restrictedRelations = [];
    public array $cascadedRelations = [];
    public array $filesToUpload = [];
    public const ADDITIONAL_PERMISSIONS = [];
    public const DISABLE_PERMISSIONS = true;
    public const DISABLE_LOG = false;

    //--------------------- casting  -------------------------------------

    //--------------------- relations -------------------------------------
    public function admin() : BelongsTo {
        return $this->belongsTo(Admin::class);
        
    }

    public function wallet() : BelongsTo {
        return $this->belongsTo(Wallet::class);
    }

    //--------------------- functions -------------------------------------

    //--------------------- scopes -------------------------------------

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOfWallet($query, $walletId)
    {
        return $query->where('wallet_id', $walletId);
    }

    public function scopeOfAdmin($query, $adminId)
    {
        return $query->where('admin_id', $adminId);
    }

    public function scopeOfReferenceType($query, $referenceType)
    {
        return $query->where('reference_type', $referenceType);
    }

    public function scopeOfReferenceId($query, $referenceId)
    {
        return $query->where('reference_id', $referenceId);
    }
}
