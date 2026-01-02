<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\SearchTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Wallet extends Model
{
    use SoftDeletes, ModelTrait, SearchTrait, HasTranslations, HasFactory;
    protected $fillable = ['balance'];
    protected array $filters = ['keyword','createdAtMin','createdAtMax'];
    protected array $searchable = [];
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
    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }
    //--------------------- functions -------------------------------------

    //--------------------- scopes -------------------------------------

}
