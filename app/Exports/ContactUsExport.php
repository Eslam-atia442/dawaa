<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ContactUsExport implements FromView, ShouldAutoSize, WithHeadings, WithEvents
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
        return view('export.contact-us-excel', [
            'records' => $this->records->toArray()
        ]);
    }

    public function headings(): array
    {
        return [
            '#',
            __('trans.name'),
            __('trans.email'),
            __('trans.phone'),
            __('trans.country.index'),
            __('trans.text_of_message'),
            __('trans.created_at'),
        ];
    }
}

