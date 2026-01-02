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

class Device extends Model implements HasMedia
{
    use  ModelTrait, SearchTrait, HasTranslations, HasMediaConversionsTrait;

    protected $guarded = [];
    protected $casts = [
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
    ];
    protected array $filters = ['keyword', 'createdAtMin', 'createdAtMax'];
    protected array $searchable = ['name', 'fcm_token'];
    protected array $dates = [];
    public array $translatable = ['name'];
    public array $restrictedRelations = [];
    public array $cascadedRelations = [];
    public array $filesToUpload = ['image'];
    public const ADDITIONAL_PERMISSIONS = [];
    public const DISABLE_PERMISSIONS    = true;
    public const DISABLE_LOG            = false;

    //--------------------- casting  -------------------------------------

    //--------------------- relations -------------------------------------

    //--------------------- functions -------------------------------------

    /**
     * Mark device as used (update last_used_at)
     */
    public function markAsUsed(): void
    {
        $this->update(['last_used_at' => now()]);
    }

    /**
     * Check if device is ready for notifications
     */
    public function isReadyForNotifications(): bool
    {
        return $this->is_active && !empty($this->fcm_token);
    }

    /**
     * Get the deviceable model (User or null for guests)
     */
    public function deviceable()
    {
        return $this->morphTo();
    }

    //--------------------- scopes -------------------------------------

    public function scopeOfActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for devices with FCM tokens
     */
    public function scopeWithFcmToken($query)
    {
        return $query->whereNotNull('fcm_token');
    }

    /**
     * Scope for specific device type
     */
    public function scopeOfDeviceType($query, string $type)
    {
        return $query->where('device_type', $type);
    }

    /**
     * Scope for Android devices
     */
    public function scopeOfAndroid($query)
    {
        return $query->where('device_type', 'android');
    }

    /**
     * Scope for iOS devices
     */
    public function scopeOfIos($query)
    {
        return $query->where('device_type', 'ios');
    }

}
