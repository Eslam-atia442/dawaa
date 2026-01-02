<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\SearchTrait;
use App\Traits\FCMNotificationTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Translatable\HasTranslations;
use App\Traits\HasMediaConversionsTrait;
use Spatie\MediaLibrary\HasMedia;
class User extends Authenticatable implements HasMedia
{
    use SoftDeletes, Notifiable, ModelTrait, SearchTrait, HasTranslations, HasFactory, HasApiTokens, FCMNotificationTrait , HasMediaConversionsTrait;

    protected $guarded = ['id'];
    protected $hidden = ['password', 'remember_token',];
    protected $casts = [
        'email_verified_at' => 'datetime',
        'code_expires_at' => 'datetime',
    ];
    protected array $filters = ['keyword','createdAtMin', 'createdAtMax','login'];
    public array $searchable = ['name', 'email', 'phone'];
    public array $translatable = [];
    public array $restrictedRelations = [];
    public array $cascadedRelations = [];
    public array $filesToUpload = ['avatar'];
    public const ADDITIONAL_PERMISSIONS = [];
    const        DISABLE_PERMISSIONS    = false;
    const        DISABLE_LOG            = false;

    // ----------------------- relations -----------------------
    public function devices(): MorphMany
    {
        return $this->morphMany(Device::class, 'deviceable');
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get active devices with FCM tokens
     */
    public function activeDevices()
    {
        return $this->devices()->ofActive()->withFcmToken();
    }

    /**
     * Get Android devices
     */
    public function androidDevices()
    {
        return $this->devices()->ofActive()->ofAndroid()->withFcmToken();
    }

    /**
     * Get iOS devices
     */
    public function iosDevices()
    {
        return $this->devices()->ofActive()->ofIos()->withFcmToken();
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function favoriteGoldFunds()
    {
        return $this->morphedByMany(GoldFund::class, 'forable', 'favorites');
    }

    public function favoriteCertificates()
    {
        return $this->morphedByMany(Certificate::class, 'forable', 'favorites');
    }

    /**
     * Check if user has active devices
     */
    public function hasActiveDevices(): bool
    {
        return $this->activeDevices()->exists();
    }

    /**
     * Send FCM notification to this user
     */
    public function sendFCMNotification(array $messageData, ?string $modelType = null, ?int $modelId = null): array
    {
        return $this->sendFCMToUser($this, $messageData, $modelType, $modelId);
    }

    public function setPasswordAttribute($input): void
    {
        if ($input) {
            $this->attributes['password'] = app('hash')->needsRehash($input) ? Hash::make($input) : $input;
        }
    }
    // ----------------------- Scopes -----------------------


}
