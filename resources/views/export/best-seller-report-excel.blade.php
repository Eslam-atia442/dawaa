@extends('export.excel_layouts.index-for-excel')
@section('content')

    <table class="table m-b-xs">
        <tbody>
        <tr style="background-color: #337ab7;color: #FFF;">
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">#</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{__('trans.product_name')}}</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{__('trans.total_quantity_sold')}}</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{__('trans.orders_count')}}</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{__('trans.total_revenue')}}</th>
        </tr>
        @forelse($records as $record)
            <tr>
                <td style="text-align: center">{{ $loop->iteration }}</td>
                <td style="text-align: center">{{ is_array($record['product_name'] ?? null) ? ($record['product_name'][app()->getLocale()] ?? '') : ($record['product_name'] ?? '') }}</td>
                <td style="text-align: center">{{ $record['total_quantity_sold'] ?? 0 }}</td>
                <td style="text-align: center">{{ $record['orders_count'] ?? 0 }}</td>
                <td style="text-align: center">{{ number_format($record['total_revenue'] ?? 0, 2) }}</td>
            </tr>
        @empty
            <tr>
                <td style="text-align: center" colspan="5">{{__('trans.no_data_found')}}</td>
            </tr>
        @endforelse
        </tbody>
    </table>
@stop
