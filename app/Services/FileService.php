<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\BaseContract;
use App\Repositories\Contracts\FileContract;

class FileService extends BaseService
{
    protected BaseContract $repository;

    public function __construct(FileContract $repository)
    {
        $this->repository = $repository;
        parent::__construct($repository);
    }

    public function createFile(UploadedFile $file, $request, $type, $fileable = null, $path = '')
    {
        $request['type'] = $type;
        $request['folder'] = $path;

        if ($fileable instanceof Model) {
            $request['fileable_id'] = $fileable->id;
            $request['fileable_type'] = class_basename($fileable);
        }
        return $this->repository->createFile($file, $request);
    }

    public function updateFile(Model $model, $request, $type, $path = '', $newFile = null)
    {
        $request['type'] = $type;
        $request['folder'] = $path;
        return $this->repository->updateFile($model, $request, $newFile);
    }

}
