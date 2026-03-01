<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ProductExport implements FromView, ShouldAutoSize, WithHeadings, WithEvents
{
    private $records;

    public function __construct($records)
    {
        // Unwrap paginator if export job passed paginator->toArray()
        $data = isset($records['data']) && is_array($records['data']) ? $records['data'] : $records;
        $this->records = collect($data);
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
        return view('export.product-excel', [
            'records' => $this->records->toArray()
        ]);
    }

    public function headings(): array
    {
        return [
            '#',
            __('trans.name'),
            __('trans.store.index'),
            __('trans.category.index'),
            __('trans.price'),
            __('trans.quantity'),
            __('trans.expiry_date'),
            __('trans.production_line_number'),
            __('trans.activate'),
            __('trans.created_at'),
        ];
    }
}

