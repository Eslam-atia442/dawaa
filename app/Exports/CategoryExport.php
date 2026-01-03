<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class CategoryExport implements FromView, ShouldAutoSize, WithHeadings, WithEvents
{
    private $records;

    public function __construct($records)
    {
        $this->records = collect($records);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getDelegate()->setRightToLeft(app()->getLocale() == 'ar');
            },
        ];
    }

    public function view(): View
    {
        return view('export.category-excel', [
            'records' => $this->records->toArray()
        ]);
    }

    public function headings(): array
    {
        return [
            '#',
            __('trans.Name'),
            __('trans.Status'),
            __('trans.Created At'),
        ];
    }
}

