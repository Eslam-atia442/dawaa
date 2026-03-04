<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ChildProductExport implements FromView, ShouldAutoSize, WithHeadings, WithEvents
{
    private $records;

    public function __construct($records, $exportId = null)
    {
        $data = isset($records['data']) && is_array($records['data']) ? $records['data'] : $records;
        $collection = collect($data);

        $locale = app()->getLocale();

        // Normalize to same shape as product export: name, store, category from parent; rest from child
        $this->records = $collection->map(function ($record) use ($locale) {
            $arr = is_array($record) ? $record : (method_exists($record, 'toArray') ? $record->toArray() : (array) $record);
            $parent = $arr['parent'] ?? [];
            $name = isset($arr['parent_id']) ? $parent['name'][app()->getLocale()] : $arr['name'][app()->getLocale()] ?? '';
            

            $rawStore = $arr['store'] ?? $parent['store'] ?? null;
            $storeName = is_array($rawStore) ? ($rawStore['name'] ?? null) : (is_object($rawStore) ? ($rawStore->name ?? null) : $rawStore);
            if (is_array($storeName)) {
                $storeName = $storeName[$locale] ?? $storeName['en'] ?? head($storeName) ?? '-';
            }
            $storeName = $storeName ?? '-';

            $rawCategory = $arr['category'] ?? $parent['category'] ?? null;
            $categoryName = is_array($rawCategory) ? ($rawCategory['name'] ?? null) : (is_object($rawCategory) ? ($rawCategory->name ?? null) : $rawCategory);
            if (is_array($categoryName)) {
                $categoryName = $categoryName[$locale] ?? $categoryName['en'] ?? head($categoryName) ?? '-';
            }
            $categoryName = $categoryName ?? '-';
 
            return [
                'name' => $name,
                'store' => ['name' => $storeName],
                'category' => ['name' => $categoryName],
                'price' => $arr['price'] ?? null,
                'quantity' => $arr['quantity'] ?? null,
                'expiry_date' => $arr['expiry_date'] ?? null,
                'production_line_number' => $arr['production_line_number'] ?? null,
                'is_active' => $arr['is_active'] ?? null,
                'created_at' => $arr['created_at'] ?? null,
                'parent_id' => $arr['parent_id'] ?? null,
            ];
        })->toArray();
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->setRightToLeft(app()->getLocale() == 'ar');
            },
        ];
    }

    public function view(): View
    {
        return view('export.product-excel', [
            'records' => $this->records,
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
