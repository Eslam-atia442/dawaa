@extends('export.excel_layouts.index-for-excel')
@section('content')

    <table class="table m-b-xs">
        <tbody>
        <tr style="background-color: #337ab7;color: #FFF;">
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">#</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{__('trans.name')}}</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{__('trans.activate')}}</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{__('trans.created_at')}}</th>
        </tr>
        @forelse($records as $record)
            <tr>
                <td style="text-align: center">{{ $loop->iteration }}</td>
                <td style="text-align: center">{{ is_array($record['name'] ?? null) ? ($record['name'][app()->getLocale()] ?? '') : ($record['name'] ?? '') }}</td>
                <td style="text-align: center">{{ ($record['is_active'] ?? false) ? __('trans.active') : __('trans.inactive') }}</td>
                <td style="text-align: center">{{ isset($record['created_at']) && $record['created_at'] ? \Carbon\Carbon::parse($record['created_at'])->format('Y-m-d H:i:s') : '' }}</td>
            </tr>
        @empty
            <tr>
                <td style="text-align: center" colspan="4">{{__('trans.no data available')}}</td>
            </tr>
        @endforelse
        </tbody>
    </table>
@stop

