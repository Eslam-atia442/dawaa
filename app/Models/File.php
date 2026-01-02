<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\SearchTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    use HasFactory, ModelTrait, SearchTrait;

    protected $fillable = ["name", "ext", "url", "type", "width", "height",
        "mime", "fileable_type", "fileable_id", "duration", "user_id",
        "custom_name", 'notes', 'size'
    ];

    public const DISABLE_LOG = true;


    public $filters = ['type', 'fileId', 'untracked', 'notType'];

    protected $appends = ['full_url'];


    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public const DISABLE_PERMISSIONS = true;

    /**
     * Get the owning fileable model.
     */
    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope('all', function (Builder $builder) {
            $builder->orderBy('id', 'Desc');
        });
        static::deleting(function ($file) { // before delete() method call this
            if (isset($file->url)) {
                if (Storage::exists($file->url)) {
                    Storage::delete($file->url);
                }
            }
        });
    }

    public function getFullUrlAttribute()
    {
        if (isset($this->url)) {
            if (!Storage::exists($this->url)) {
                return asset('/assets/img/avatars/default.png');
            }
            return Storage::url($this->url);
        }

        return null;
    }

    public function fileable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // -------------------------- scopes  --------------------------

    public function scopeActive( $query )
    {
        return $query->where('is_active', 1);
    }
    public function scopeOfUntracked($query)
    {
        return $query->whereNull('fileable_type')->where('created_at', '<', now()->subMinutes(90));
    }

    public function scopeOfType($query, $value)
    {
        if (empty($value)) {
            return $query;
        }
        return $query->whereIn('type', (array)$value);
    }

    public function scopeOfFileId($query, $value)
    {
        if (empty($value)) {
            return $query;
        }
        return $query->whereIn('fileable_id', (array)$value);
    }

    public function scopeOfNotType($query, $value)
    {
        if (empty($value)) {
            return $query;
        }
        return $query->whereNotIn('type', (array)$value);
    }


    public function getOriginalNameAttribute()
    {
        return $this->custom_name ?? substr($this->name, strpos($this->name, '-') + 1);
    }

}
