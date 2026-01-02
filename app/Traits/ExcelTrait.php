<?php

namespace App\Traits;

use App\Exports\GenericViewExport;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;

trait ExcelTrait
{
    public function generateExcelFromView($contract, $file, $view, $path): JsonResponse
    {
        $data = $contract->search(request()->all(), [], true, false, request('exportLimit') ?? 10000);
        $file = now()->format('Y-m-d-H-i-s')."-".$file;
        Excel::store(new GenericViewExport($data, $view), "downloaded/excel/reports/$file");
        return response()->json([
            'url' => "$path/$file"
        ]);
    }
}
