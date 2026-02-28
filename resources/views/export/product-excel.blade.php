@extends('export.excel_layouts.index-for-excel')
@section('content')

    <table class="table m-b-xs">
        <tbody>
        <tr style="background-color: #337ab7;color: #FFF;">
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">#</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{ __('trans.name') }}</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{ __('trans.store.index') }}</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{ __('trans.city.index') }}</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{ __('trans.category.index') }}</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{ __('trans.price') }}</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{ __('trans.quantity') }}</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{ __('trans.expiry_date') }}</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{ __('trans.production_line_number') }}</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{ __('trans.activate') }}</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{ __('trans.created_at') }}</th>
        </tr>
        @forelse($records as $record)
            <tr>
                <td style="text-align: center">{{ $loop->iteration }}</td>
                <td style="text-align: center">{{ is_array($record['name'] ?? null) ? ($record['name'][app()->getLocale()] ?? '') : ($record['name'] ?? '') }}</td>
                <td style="text-align: center">{{ is_array($record['store']['name'] ?? null) ? ($record['store']['name'][app()->getLocale()] ?? '') : ($record['store']['name'] ?? '-') }}</td>
                <td style="text-align: center">{{ is_array($record['city']['name'] ?? null) ? ($record['city']['name'][app()->getLocale()] ?? '') : ($record['city']['name'] ?? '-') }}</td>
                <td style="text-align: center">{{ is_array($record['category']['name'] ?? null) ? ($record['category']['name'][app()->getLocale()] ?? '') : ($record['category']['name'] ?? '-') }}</td>
                <td style="text-align: center">{{ number_format($record['price'] ?? 0, 2) }}</td>
                <td style="text-align: center">{{ $record['quantity'] ?? 0 }}</td>
                <td style="text-align: center">{{ isset($record['expiry_date']) && $record['expiry_date'] ? \Carbon\Carbon::parse($record['expiry_date'])->format('Y-m-d') : '-' }}</td>
                <td style="text-align: center">{{ $record['production_line_number'] ?? '-' }}</td>
                <td style="text-align: center">{{ ($record['is_active'] ?? false) ? __('trans.active') : __('trans.inactive') }}</td>
                <td style="text-align: center">{{ isset($record['created_at']) && $record['created_at'] ? \Carbon\Carbon::parse($record['created_at'])->format('Y-m-d H:i:s') : '' }}</td>
            </tr>
        @empty
            <tr>
                <td style="text-align: center" colspan="11">{{ __('trans.No data available') }}</td>
            </tr>
        @endforelse
        </tbody>
    </table>
@stop
