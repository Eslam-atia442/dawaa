@extends('export.excel_layouts.index-for-excel')
@section('content')

    <table class="table m-b-xs">
        <tbody>
        <tr style="background-color: #337ab7;color: #FFF;">
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">#</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{__('trans.Name')}}</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{__('trans.Email')}}</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{__('trans.Phone')}}</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{__('trans.Created At')}}</th>
        </tr>
        @forelse($records as $record)
            <tr>
                <td style="text-align: center">{{ $loop->iteration }}</td>
                <td style="text-align: center">{{ $record['name'] ?? '' }}</td>
                <td style="text-align: center">{{ $record['email'] ?? '' }}</td>
                <td style="text-align: center">{{ $record['phone'] ?? '' }}</td>
                <td style="text-align: center">{{ isset($record['created_at']) && $record['created_at'] ? \Carbon\Carbon::parse($record['created_at'])->format('Y-m-d H:i:s') : '' }}</td>
            </tr>
        @empty
            <tr>
                <td style="text-align: center" colspan="5">{{__('trans.No data available')}}</td>
            </tr>
        @endforelse
        </tbody>
    </table>
@stop

