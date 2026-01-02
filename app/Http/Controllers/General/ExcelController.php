<?php

namespace App\Http\Controllers\General;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Exports\MasterExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{
    public function __construct()
    {
        if (ob_get_length()) {
            ob_end_clean(); // this
            ob_start(); // and this
        }
    }

    public function master($export , Request $request)
    {
         $data =  $this->$export($export);
         return $this->masterExport(
             $export,
             $data['cols'],
             $data['values'],
     $data['relations'] ?? [] ,
             $request);
    }

    public function User()
    {
        return [
            'cols' => ['#', __('trans.name'), __('trans.email'), __('trans.phone')],
            'relations' =>  [],
            'values' =>  ['id', 'name', 'email', 'phone']
        ];
    }

       public function admin()
    {
        return [
            'cols' => ['#', __('trans.name'), __('trans.email'), __('trans.phone')  ],
            'relations' =>  [],
            'values' =>  ['id', 'name', 'email', 'phone' ]
        ];
    }


    public function Category()
    {
        return [
            'cols' => ['#', __('trans.name'), __('trans.email'), __('trans.phone')],
            'relations' =>  [],
            'values' =>  ['id', 'name', 'email', 'phone']
        ];
    }
    public function masterExport($model,$cols,$values,$relations,$request)
    {
        $folderNmae = strtolower(Str::plural(class_basename($model)));
        $service = app("App\Services\\$model"."Service");
        request()->merge(['page' => false, 'limit'=> false ]);
        $records = $service->search([], $relations ,$request->all());
         return Excel::download(new MasterExport($records, 'master-excel', ['cols' => $cols, 'values' => $values]), $folderNmae.'-reports-' . Carbon::now()->format('Y-m-d') . '.xlsx');
    }
}
