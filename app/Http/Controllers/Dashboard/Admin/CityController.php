<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\BaseWebController;
use App\Http\Requests\Admin\City\CreateRequest;
use App\Http\Requests\Admin\City\UpdateRequest;
use App\Models\City;
use App\Services\RegionService;
use Exception;
use App\Services\CityService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;


class CityController extends BaseWebController
{

    public object $regionService;
    public object $service;
    public string $table;
    public string $guard;
    public array $relations;

    public function __construct(
        RegionService $regionService,
        CityService   $service,
                      $table = 'cities',
                      $guard = 'admin'
    )
    {
        $this->regionService = $regionService;
        $this->service = $service;
        $this->table = $table;
        $this->guard = $guard;
        $this->relations = ['region'];
        parent::__construct($this->service, $this->table, $this->guard, $this->relations, 'city');
    }

    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            $rows = $this->service->search(request()->all(), $this->relations);
            $html = view('dashboard.' . $this->guard . '.' . $this->table . '.table', compact('rows'))->render();
            return response()->json(['html' => $html]);
        }
        $regions = $this->regionService->search([], [], ['limit' => false, 'page' => false]);
        return view('dashboard.' . $this->guard . '.' . $this->table . '.index', compact('regions'));
    }

    public function store(CreateRequest $request): JsonResponse
    {
        try {
            $this->service->create($request->validated());
            return response()->json(['url' => route($this->guard . '.' . $this->table . '.index')]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function create(): View
    {
        $regions = $this->regionService->search([], [], ['limit' => false, 'page' => false]);
        return view('dashboard.' . $this->guard . '.' . $this->table . '.create', compact('regions'));
    }

    public function edit($id): View
    {
        $row = $this->service->find($id, $this->relations);
        $regions = $this->regionService->search([], [], ['limit' => false, 'page' => false]);
        return view('dashboard.' . $this->guard . '.' . $this->table . '.edit', compact('row', 'regions'));
    }

    public function update(UpdateRequest $request, City $city): JsonResponse
    {
        try {
            $this->service->update($city, $request->validated());
            return response()->json(['url' => route($this->guard . '.' . $this->table . '.index')]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function destroyMultiple(Request $request)
    {
        $request->validate([
            'data' => 'required|json'
        ]);

        return $this->destroy($request->input('data'));
    }
}
