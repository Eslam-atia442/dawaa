<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\BaseWebController;
use App\Services\ActivityLogService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;


class ActivityLogController extends BaseWebController
{
    public object $service;
    public string $table;
    public string $guard;
    public array $relations;

    public function __construct(
        ActivityLogService $service,
                           $table = 'activity_logs',
                           $guard = 'admin'
    )
    {
        $this->service = $service;
        $this->table = $table;
        $this->guard = $guard;
        $this->relations = ['actor'];
        parent::__construct($this->service, $this->table, $this->guard, $this->relations);
    }

    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            $rows = $this->service->search(request()->all(), $this->relations);
            $html = view('dashboard.' . $this->guard . '.' . $this->table . '.table', compact('rows'))->render();
            return response()->json(['html' => $html]);
        }
        return view('dashboard.' . $this->guard . '.' . $this->table . '.index');
    }


}
