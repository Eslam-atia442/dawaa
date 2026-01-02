<?php

namespace App\Models;

use App\Traits\ModelTrait;
use App\Traits\SearchTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Export extends Model
{
    use HasFactory, ModelTrait, SearchTrait, SoftDeletes;

    protected $fillable = [
        'name',
        'model',
        'status',
        'file_path',
        'user_id',
        'parameters',
        'total_records',
        'completed_at',
        'error_message'
    ];

    protected array $filters = ['keyword', 'status', 'model', 'user', 'createdAtMin', 'createdAtMax'];
    protected array $searchable = ['name', 'model'];

    protected $casts = [
        'parameters' => 'array',
        'completed_at' => 'datetime',
        'total_records' => 'integer'
    ];

    public const ADDITIONAL_PERMISSIONS = [];
    public const DISABLE_PERMISSIONS = false;
    public const DISABLE_LOG = false;

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'user_id');
    }

    // Scopes
    public function scopeOfStatus($query, $status)
    {
        if (empty($status)) {
            return $query;
        }
        return $query->whereIn('status', (array)$status);
    }

    public function scopeOfModel($query, $model)
    {
        if (empty($model)) {
            return $query;
        }
        return $query->whereIn('model', (array)$model);
    }

    public function scopeOfUser($query, $userId)
    {
        if (empty($userId)) {
            return $query;
        }
        return $query->whereIn('user_id', (array)$userId);
    }

    // Accessors
    public function getFileUrlAttribute()
    {
        if ($this->file_path && Storage::exists($this->file_path)) {
            return Storage::url($this->file_path);
        }
        return null;
    }

    public function getFileNameAttribute()
    {
        return $this->name . '_' . $this->created_at->format('Y-m-d_H-i-s') . '.xlsx';
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'processing' => 'warning',
            'ready' => 'success',
            'failed' => 'danger',
            default => 'secondary'
        };
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'processing' => __('trans.Processing'),
            'ready' => __('trans.Ready to Download'),
            'failed' => __('trans.Failed'),
            default => __('trans.Unknown')
        };
    }

    // Methods
    public function isReady(): bool
    {
        return $this->status === 'ready';
    }

    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function markAsReady(string $filePath, int $totalRecords = 0): void
    {
        $this->update([
            'status' => 'ready',
            'file_path' => $filePath,
            'total_records' => $totalRecords,
            'completed_at' => now(),
        ]);
    }

    public function markAsFailed(string $errorMessage = null): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
            'completed_at' => now(),
        ]);
    }

    protected static function boot(): void
    {
        parent::boot();

        static::deleting(function ($export) {
            // Delete the file when export record is deleted
            if ($export->file_path && Storage::exists($export->file_path)) {
                Storage::delete($export->file_path);
            }
        });
    }
}
