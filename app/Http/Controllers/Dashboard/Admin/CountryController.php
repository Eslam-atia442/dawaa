<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\BaseWebController;
use App\Http\Requests\Admin\Country\CreateRequest;
use App\Http\Requests\Admin\Country\UpdateRequest;
use App\Models\Country;
use Exception;
use App\Services\CountryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;


class CountryController extends BaseWebController
{
    public object $service;
    public string $table;
    public string $guard;
    public array $relations;

    public function __construct(
        CountryService $service,
                       $table = 'countries',
                       $guard = 'admin'
    )
    {
        $this->service   = $service;
        $this->table     = $table;
        $this->guard     = $guard;
        $this->relations = [];
        parent::__construct($this->service, $this->table, $this->guard, $this->relations, 'country');
    }

    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            request()->merge([
                'order' => ['is_active' => 'desc'],
            ]);
            $rows = $this->service->search(request()->all(), $this->relations);
            $html = view('dashboard.' . $this->guard . '.' . $this->table . '.table', compact('rows'))->render();
            return response()->json(['html' => $html]);
        }
        return view('dashboard.' . $this->guard . '.' . $this->table . '.index');
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

    public function update(UpdateRequest $request, Country $country): JsonResponse
    {
        try {
            $this->service->update($country, $request->validated());
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
