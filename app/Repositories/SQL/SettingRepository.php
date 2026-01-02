<?php

namespace App\Repositories\SQL;

use App\Models\File;
use App\Models\Setting;
use App\Repositories\Contracts\SettingContract;

class SettingRepository extends BaseRepository implements SettingContract
{
    /**
     * SettingRepository constructor.
     * @param Setting $model
     */
    public function __construct(Setting $model)
    {
        parent::__construct($model);
    }

    public function updateSetting($request): bool
    {
        $files = $this->model->filesToUpload;

        foreach ($files as $fileKey) {
            if (!empty($request[$fileKey]) && $request[$fileKey] instanceof \Illuminate\Http\UploadedFile) {
                $uploadedFile = $request[$fileKey];

                $element = $this->query->where('key', $fileKey)->first();

                if ($element) {
                    $element->update(['value' => $fileKey]);

                    // Clear old media
                    if ($element->hasMedia($fileKey)) {
                        $element->clearMediaCollection($fileKey);
                    }

                    // Attach new file
                    $element->addMedia($uploadedFile)->toMediaCollection($fileKey);
                }
            }
        }
        foreach ($request as $key => $value) {
            $this->query->updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return true;
    }
}
