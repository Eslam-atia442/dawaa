<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\InteractsWithMedia;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Spatie\MediaLibrary\MediaCollections\Models\Media;


trait HasMediaConversionsTrait
{
    use InteractsWithMedia;

    public function addOptimizedMedia(
        UploadedFile $file,
        string       $collectionName = 'default',
        ?int         $quality = 90 // Set to null or 100 for max quality
    )
    {
        $extension = $file->getClientOriginalExtension();
        if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
            $media = $this->addMedia($file)
                ->usingFileName($file->hashName())
                ->toMediaCollection($collectionName);
            return $media;
        }
        $filename = Str::uuid() . '.' . $extension;
        $tempPath = storage_path('app/temp');

        if (!file_exists($tempPath)) {
            mkdir($tempPath, 0777, true);
        }

        $tempFullPath = $tempPath . '/' . $filename;

        $manager = new ImageManager(new GdDriver());

        // Just open and re-save (optionally with lower quality)
        $image = $manager->read($file->getRealPath())
            ->save($tempFullPath, quality: $quality ?? 100);

        if (!file_exists($tempFullPath)) {
            throw new \Exception("Optimized image was not saved to temp path.");
        }

        $media = $this->addMedia($tempFullPath)
            ->usingFileName($file->hashName())
            ->toMediaCollection($collectionName);

        if (file_exists($tempFullPath)) {
            unlink($tempFullPath);
        }

        return $media;
    }

    public function registerMediaCollections(): void
    {
        $files = $this->filesToUpload ?? [];
        foreach ($files as $file) {
            $this
                ->addMediaCollection($file)
                ->useFallbackUrl(asset('/assets/img/avatars/def2.png'))
                ->useFallbackPath(asset('/assets/img/avatars/def2.png'));
        }
    }
}
