<?php

namespace App\Repositories\SQL;

use App\Models\File;
use App\Repositories\Contracts\FileContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileRepository extends BaseRepository implements FileContract
{


    /**
     * FileRepository constructor.
     * @param File $model
     */
    public function __construct(File $model)
    {
        parent::__construct($model);
    }

    public function createMany(array $attributes = []): bool|File|array|null
    {
        $files = [];
        foreach ($attributes as $attribute) {
            $files[] = $this->create($attribute);
        }
        return $files;
    }

    public function create(array $attributes = []): mixed
    {

        if (!empty($attributes["current_id"])) {
            $file_obj = $this->find($attributes["current_id"]);
            if ($file_obj) {
                $file_obj->fill($attributes);
                $file_obj->update();
                return $file_obj;
            }
        }
        //check if $attributes["file"] is array
        if (is_array($attributes["file"])) {

            foreach ($attributes["file"] as $key => $value) {
                $attributes = $this->upload($value, $attributes);
            }

        } else {


            $attributes = $this->upload($attributes["file"], $attributes);
        }

        return parent::create($attributes);
    }

    public function remove($model): mixed
    {
        $model = $this->find($model->id);
        if (Storage::exists($model->url)) {
            Storage::delete($model->url);
        }
        return parent::remove($model);
    }

    public function createFile(UploadedFile $file, $request): File
    {
        $attributes = $this->upload($file, $request);
        return parent::create($attributes);
    }

    public function updateFile($model, array $attributes = [], $newFile = null)//: mixed
    {
        if ($newFile instanceof UploadedFile) {
            $this->deleteFile($model, false);//delete old file
            $attributes = $this->upload($newFile, $attributes);
        }
        return parent::update($model, $attributes);
    }


}
