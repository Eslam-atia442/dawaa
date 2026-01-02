<?php

namespace App\Models;

use App\Traits\ModelTrait;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use \Spatie\Permission\Models\Permission as BasePermission;

class Permission extends BasePermission
{
    use ModelTrait;

    public const ADDITIONAL_PERMISSIONS = [];
    protected static mixed $abilities;
    protected static mixed $models;
    protected static mixed $permissions;
    protected array        $filters = [];

    public const DISABLE_PERMISSIONS = true;
    public const DISABLE_LOG         = false;

    public static function defaultPermissions(): array{
        self::$permissions = [];
        self::$abilities   = collect(['create', 'read', 'read-all', 'update', 'delete']);
        $modelFiles        = Storage::disk('app')->files('Models');

        $modelFiles = array_unique(array_filter($modelFiles, function ($model){
            return !str_ends_with($model, '~');
        }));


        self::$models = collect($modelFiles)->map(function ($modelFile){
            $model      = str_replace('.php', '', $modelFile);
            $model      = str_replace('Models/', '', $model);
            $modelClass = 'App\\Models\\' . str_replace('/', '\\', $model);

            self::$abilities->map(function ($ability) use ($model, $modelClass){
                if ($model != 'Copy')
                    if (!defined("$modelClass::DISABLE_PERMISSIONS") || !$modelClass::DISABLE_PERMISSIONS) {
                        $perm                = $ability . Str::lower(implode('-', preg_split('/(?=[A-Z])/', $model)));
                        self::$permissions[] = ['name' => $perm, 'action' => $ability, 'model' => $model, 'guard_name' => 'admin'];
                    }
            });
            if (defined("$modelClass::ADDITIONAL_PERMISSIONS")) {

                foreach ($modelClass::ADDITIONAL_PERMISSIONS as $additionalPermission) {

                    self::$permissions[] = [
                        'name'       => $additionalPermission . Str::lower(implode('-', preg_split('/(?=[A-Z])/', $model))),
                        'action'     => $additionalPermission,
                        'model'      => $model,
                        'guard_name' => 'admin'
                    ];

                }

            }
        });

        return self::$permissions;
    }
}
