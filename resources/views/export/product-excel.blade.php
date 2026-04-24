@extends('export.excel_layouts.index-for-excel')
@section('content')


@php
$firstRecord = $records[0];
$locale = app()->getLocale();
$rawName = data_get($firstRecord, 'name');
$name = is_array($rawName) ? ($rawName[$locale] ?? $rawName['en'] ?? head($rawName) ?? '-') : ($rawName ?? '-');
$price = number_format((float) (data_get($firstRecord, 'price') ?? 0), 2);
$storeName = data_get($firstRecord, 'store.name') ?? data_get($firstRecord, 'store') ?? '-';
$storeName = is_array($storeName) ? ($storeName[$locale] ?? $storeName['en'] ?? head($storeName) ?? '-') : ($storeName ?? '-');
$categoryName = data_get($firstRecord, 'category.name') ?? data_get($firstRecord, 'category') ?? '-';
$categoryName = is_array($categoryName) ? ($categoryName[$locale] ?? $categoryName['en'] ?? head($categoryName) ?? '-') : ($categoryName ?? '-');
$quantity = (int) (data_get($firstRecord, 'quantity') ?? 0);
$is_parent = data_get($firstRecord, 'parent_id') ?true : false;
@endphp



<table class="table m-b-xs">
    <tbody>
        <tr style="background-color: #337ab7;color: #FFF;">
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">#</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{ __('trans.name') }}</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{ __('trans.store.index') }}</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{ __('trans.category.index') }}</th>
            @if($is_parent) <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{ __('trans.price') }}</th> @endif
            @if($is_parent) <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{ __('trans.quantity') }}</th> @endif
            @if($is_parent) <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{ __('trans.expiry_date') }}</th> @endif
            @if($is_parent) <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{ __('trans.production_line_number') }}</th> @endif
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{ __('trans.activate') }}</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{ __('trans.created_at') }}</th>
        </tr>
        @forelse($records as $record)
        @php
        $locale = app()->getLocale();
        $rawName = data_get($record, 'name');
        $name = is_array($rawName) ? ($rawName[$locale] ?? $rawName['en'] ?? head($rawName) ?? '-') : ($rawName ?? '-');
        $price = number_format((float) (data_get($record, 'price') ?? 0), 2);
        $storeName = data_get($record, 'store.name') ?? data_get($record, 'store') ?? '-';
        $storeName = is_array($storeName) ? ($storeName[$locale] ?? $storeName['en'] ?? head($storeName) ?? '-') : ($storeName ?? '-');
        $categoryName = data_get($record, 'category.name') ?? data_get($record, 'category') ?? '-';
        $categoryName = is_array($categoryName) ? ($categoryName[$locale] ?? $categoryName['en'] ?? head($categoryName) ?? '-') : ($categoryName ?? '-');
        $quantity = (int) (data_get($record, 'quantity') ?? 0);
        $is_parent = data_get($record, 'parent_id') ?true : false;
        $expiry = data_get($record, 'expiry_date');
        @endphp
        <tr>
            <td style="text-align: center">{{ $loop->iteration }}</td>
            <td style="text-align: center">{{ $name }}</td>
            <td style="text-align: center">{{ $storeName }}</td>
            <td style="text-align: center">{{ $categoryName }}</td>
            @if($is_parent) <td style="text-align: center">{{ $price  }}</td> @endif
            @if($is_parent) <td style="text-align: center">{{ $quantity }}</td> @endif
            @if($is_parent) <td style="text-align: center">
                {{ $expiry ? \Carbon\Carbon::parse($expiry)->format('Y-m-d') : '-' }}
            </td> @endif
            @if($is_parent) <td style="text-align: center">{{ data_get($record, 'production_line_number') ?? '-' }}</td> @endif
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