<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ProductQuantityHistoryExport implements FromView, ShouldAutoSize, WithHeadings, WithEvents
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
        return view('export.product-quantity-history-excel', [
            'records' => $this->records->toArray()
        ]);
    }

    public function headings(): array
    {
        return [
            '#',
            __('trans.type'),
            __('trans.quantity_change'),
            __('trans.quantity_before'),
            __('trans.quantity_after'),
            __('trans.reason'),
            __('trans.reference'),
            __('trans.note'),
            __('trans.admins'),
            __('trans.created_at'),
        ];
    }
}
