<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class BaseWebController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    public object $service;
    public string $guard;
    public string $table;
    public array $relations = [];


    public function __construct($service, $table, $guard, $relations, bool|string $applyPermissions = '')
    {
        $this->service = $service;
        $this->table = $table;
        $this->guard = $guard;
        $this->relations = $relations;
        if (request()->has('embed')) {
            $this->parseIncludes(request('embed'));
        }


        if (!empty($applyPermissions)) {
             $this->applyCrudPermissions($applyPermissions);
        }
    }

    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            $rows = $this->service->search(request()->all(), $this->relations);
            $html = view('dashboard.' . $this->guard . '.' . $this->table . '.table', compact('rows'))->render();
            return response()->json(['html' => $html  ]);
        }
        return view('dashboard.' . $this->guard . '.' . $this->table . '.index');
    }

    public function create(): View
    {
        return view('dashboard.' . $this->guard . '.' . $this->table . '.create');
    }

    public function edit($id): View
    {
        $row = $this->service->find($id, $this->relations);
        return view('dashboard.' . $this->guard . '.' . $this->table . '.edit', compact('row'));
    }

    public function show($id): View
    {
        $row = $this->service->find($id, $this->relations);
        return view('dashboard.' . $this->guard . '.' . $this->table . '.show', compact('row'));
    }

    public function destroy($ids): JsonResponse
    {
        try {
            $this->service->remove($ids);
            return response()->json(['url' => route($this->guard . '.' . $this->table . '.index')]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()] , 400);
        }
    }


    // base methods
    protected function parseIncludes($embed): void
    {
        $this->relations = explode(',', $embed);
    }

    public function applyCrudPermissions($name): void
    {

        $name = lcfirst($name);
        $name = str()->lower(implode('-', preg_split('/(?=[A-Z])/', $name)));
        $this->middleware('permission:read-all-' . $name)->only(['index']);
        $this->middleware('permission:read-' . $name)->only([ 'show']);
        $this->middleware('permission:create-' . $name)->only(['create', 'store']);
        $this->middleware('permission:update-' . $name)->only(['edit', 'update']);
        $this->middleware('permission:delete-' . $name)->only(['destroy']);
    }

    public function toggleField(Request $request, $model, $key)
    { 
        return $this->service->toggleField( $model , $key);
    }
}
