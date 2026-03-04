@extends('export.excel_layouts.index-for-excel')
@section('content')

<table class="table m-b-xs">
    <tbody>
        <tr style="background-color: #337ab7;color: #FFF;">
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">#</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{ __('trans.name') }}</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{ __('trans.store.index') }}</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{ __('trans.category.index') }}</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{ __('trans.price') }}</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{ __('trans.quantity') }}</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{ __('trans.expiry_date') }}</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{ __('trans.production_line_number') }}</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{ __('trans.activate') }}</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{ __('trans.created_at') }}</th>
        </tr>
        @forelse($records as $record)
        @php
            $locale = app()->getLocale();
            $name = $record['name'];
           
            $storeName = data_get($record, 'store.name') ?? data_get($record, 'store') ?? '-';
            $storeName = is_array($storeName) ? ($storeName[$locale] ?? $storeName['en'] ?? head($storeName) ?? '-') : ($storeName ?? '-');
            $categoryName = data_get($record, 'category.name') ?? data_get($record, 'category') ?? '-';
            $categoryName = is_array($categoryName) ? ($categoryName[$locale] ?? $categoryName['en'] ?? head($categoryName) ?? '-') : ($categoryName ?? '-');
        @endphp
        <tr>
            <td style="text-align: center">{{ $loop->iteration }}</td>
            <td style="text-align: center">{{ $name }}</td>
            <td style="text-align: center">{{ $storeName }}</td>
            <td style="text-align: center">{{ $categoryName }}</td>
            <td style="text-align: center">{{ number_format((float) (data_get($record, 'price') ?? 0), 2) }}</td>
            <td style="text-align: center">{{ (int) (data_get($record, 'quantity') ?? 0) }}</td>
            <td style="text-align: center">
                @php $expiry = data_get($record, 'expiry_date'); @endphp
                {{ $expiry ? \Carbon\Carbon::parse($expiry)->format('Y-m-d') : '-' }}
            </td>
            <td style="text-align: center">{{ data_get($record, 'production_line_number') ?? '-' }}</td>
            <td style="text-align: center">{{ (bool) (data_get($record, 'is_active') ?? false) ? __('trans.active') : __('trans.inactive') }}</td>
            <td style="text-align: center">
                @php $created = data_get($record, 'created_at'); @endphp
                {{ $created ? \Carbon\Carbon::parse($created)->format('Y-m-d H:i:s') : '' }}
            </td>
        </tr>
        @empty
        <tr>
            <td style="text-align: center" colspan="10">{{ __('trans.No data available') }}</td>
        </tr>
        @endforelse
    </tbody>
</table>
@stop