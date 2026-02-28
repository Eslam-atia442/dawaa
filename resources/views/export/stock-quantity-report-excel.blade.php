@extends('export.excel_layouts.index-for-excel')
@section('content')

    <table class="table m-b-xs">
        <tbody>
        <tr style="background-color: #337ab7;color: #FFF;">
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">#</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{__('trans.child_product_name')}}</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{__('trans.parent_product')}}</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{__('trans.quantity')}}</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{__('trans.price')}}</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{__('trans.store_name')}}</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{__('trans.expiry_date')}}</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{__('trans.production_line_number')}}</th>
        </tr>
        @forelse($records as $record)
            <tr>
                <td style="text-align: center">{{ $loop->iteration }}</td>
                <td style="text-align: center">{{ is_array($record['product_name'] ?? null) ? ($record['product_name'][app()->getLocale()] ?? '') : ($record['product_name'] ?? '') }}</td>
                <td style="text-align: center">{{ is_array($record['parent_product_name'] ?? null) ? ($record['parent_product_name'][app()->getLocale()] ?? '') : ($record['parent_product_name'] ?? '') }}</td>
                <td style="text-align: center">{{ $record['quantity'] ?? 0 }}</td>
                <td style="text-align: center">{{ number_format($record['price'] ?? 0, 2) }}</td>
                <td style="text-align: center">{{ is_array($record['store_name'] ?? null) ? ($record['store_name'][app()->getLocale()] ?? '') : ($record['store_name'] ?? '-') }}</td>
                <td style="text-align: center">{{ $record['expiry_date'] ?? '-' }}</td>
                <td style="text-align: center">{{ $record['production_line_number'] ?? '-' }}</td>
            </tr>
        @empty
            <tr>
                <td style="text-align: center" colspan="8">{{ __('trans.No data available') }}</td>
            </tr>
        @endforelse
        </tbody>
    </table>
@stop
