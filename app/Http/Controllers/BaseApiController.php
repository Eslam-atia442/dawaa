<?php

namespace App\Http\Controllers;
use App\Services\BaseService;
use App\Traits\BaseApiResponseTrait;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class BaseApiController extends Controller
{
    use BaseApiResponseTrait;

    protected bool $order = true;
    protected BaseService $service;
    protected mixed $modelResource;
    protected array $relations = [];

    /**
     * BaseApiController constructor.
     *
     * @param \App\Services\BaseService $service
     * @param mixed $modelResource
     * @param bool|string $applyPermissions
     */
    public function __construct(BaseService $service ,mixed $modelResource, bool|string $applyPermissions = ''  )
    {

        DB::transaction(function () {
            // Your queries or transactional code
        }, 5);  //
        $this->service = $service;
        $this->modelResource = $modelResource;

        // Include embedded data
        if (request()->has('embed')) {
            $this->parseIncludes(request('embed'));
        }

        if (!empty($applyPermissions)) {
            $this->applyCrudPermissions($applyPermissions);
        }
    }

    /**
     * index() Display a listing of the resource.
     *
     * @return mixed
     */
    public function index(): mixed
    {
        $models = $this->service->search([], $this->relations);

        $groupBy = request('groupBy');
        if ($groupBy)
            return $this->respondWithGroupByCollection($models, $groupBy);
        return $this->respondWithCollection($models);
    }


    /**
     * parseIncludes() used to explode embed relations array
     *
     * @param $embed
     */
    protected function parseIncludes($embed): void
    {
        $this->relations = explode(',', $embed);
    }

    /**
     * respond() used to return resource with status and headers
     *
     * @param $resources
     * @param array $headers
     * @return mixed
     */
    protected function respond($resources, array $headers = []): mixed
    {
        return $resources
            ->additional(['status' => $this->getStatusCode()])
            ->response()
            ->setStatusCode($this->getStatusCode())
            ->withHeaders($headers);
    }

    /**
     * respondWithCollection() used to take collection
     * and return its data transformed by resource response
     *
     * @param $collection
     * @param int|null $statusCode
     * @param array $headers
     * @return mixed
     */
    protected function respondWithCollection($collection, int $statusCode = null, array $headers = []): mixed
    {
        $statusCode = $statusCode ?? Response::HTTP_OK;
        $resources = forward_static_call([$this->modelResource, 'collection'], $collection);
        return $this->setStatusCode($statusCode)->respond($resources, $headers);
    }

    /**
     * respondWithGroupByCollection() used to take group by collection
     * and return its data transformed by resource response
     *
     * @param $models
     * @param string $groupBy
     * @param int|null $statusCode
     * @param array $headers
     * @return mixed
     */
    protected function respondWithGroupByCollection($models, string $groupBy, int $statusCode = null, array $headers = []): mixed
    {
        $statusCode = $statusCode ?? Response::HTTP_OK;
        $models = $models->map(function ($items, $key) use ($groupBy) {
            $model = $items->first()->getModel();
            $casts = $model->getCasts();
            $groupBy = str_replace('.value', '', $groupBy);
            if (array_key_exists($groupBy, $casts) && preg_match("/\bConstants\b/i", $casts[$groupBy])) {
                $key = $model->getCasts()[$groupBy]::getLabels()[$key];
            }
            return [
                'groupBy' => $key,
                'items' => forward_static_call([$this->modelResource, 'collection'], $items)
            ];
        })->all();
        return $this->respondWithArray(['status' => $statusCode, 'data' => $models], $headers);
    }

    /**
     * respondWithModel() used to return result with one model relation
     *
     * @param $model
     * @param int|null $statusCode
     * @param array $headers
     * @return mixed
     */
    protected function respondWithModel($model, int $statusCode = null, array $headers = []): mixed
    {
        $statusCode = $statusCode ?? Response::HTTP_OK;
        $resource = new $this->modelResource($model->load($this->relations)); // ???
        return $this->setStatusCode($statusCode)->respond($resource, $headers);
    }

    public function applyCrudPermissions($name): void
    {
        $name = lcfirst($name);
        $name = str()->lower(implode('-', preg_split('/(?=[A-Z])/', $name)));

        // $this->middleware('permission:read-' . $name)->only(['index', 'show']);
        $this->middleware('permission:create-' . $name)->only(['store']);
        $this->middleware('permission:update-' . $name)->only(['update']);
        $this->middleware('permission:delete-' . $name)->only(['destroy']);
    }
}
