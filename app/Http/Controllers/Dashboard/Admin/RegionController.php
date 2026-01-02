<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\BaseWebController;
use App\Http\Requests\Admin\Region\CreateRequest;
use App\Http\Requests\Admin\Region\UpdateRequest;
use App\Models\Region;
use App\Services\CountryService;
use Exception;
use App\Services\RegionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;


class RegionController extends BaseWebController
{
    public object $service;
    public object $countryService;
    public string $table;
    public string $guard;
    public array $relations;

    public function __construct(
        RegionService  $service,
        CountryService $countryService,
                       $table = 'regions',
                       $guard = 'admin'
    )
    {
        $this->service = $service;
        $this->countryService = $countryService;
        $this->table = $table;
        $this->guard = $guard;
        $this->relations = ['country'];
        parent::__construct($this->service, $this->table, $this->guard, $this->relations, 'region');
    }

    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            $rows = $this->service->search(request()->all(), $this->relations);
            $html = view('dashboard.' . $this->guard . '.' . $this->table . '.table', compact('rows'))->render();
            return response()->json(['html' => $html  ]);
        }
        $countries = $this->countryService->search([], [], ['limit' => false, 'page' => false]);

        return view('dashboard.' . $this->guard . '.' . $this->table . '.index' , compact('countries'));
    }
    public function create(): View
    {
        $countries = $this->countryService->search(['limit' => false, 'page' => false , 'active' => true ], [], []);
        return view('dashboard.' . $this->guard . '.' . $this->table . '.create', compact('countries'));
    }

    public function edit($id): View
    {
        $row = $this->service->find($id, $this->relations);
        $countries = $this->countryService->search(['limit' => false, 'page' => false , 'active' => true ], [], []);
        return view('dashboard.' . $this->guard . '.' . $this->table . '.edit', compact('row', 'countries'));
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

    public function update(UpdateRequest $request, Region $region): JsonResponse
    {
        try {
            $this->service->update($region, $request->validated());
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
