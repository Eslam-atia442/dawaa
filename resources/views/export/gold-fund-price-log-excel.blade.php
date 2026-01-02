@extends('export.excel_layouts.index-for-excel')
@section('content')

    @if(!empty($fundName))
        <table class="table m-b-xs">
            <tbody>
            <tr>
                <td colspan="5" style="text-align: center; font-weight: bold; font-size: 14px; background-color: #f0f0f0;">
                    {{ __('trans.price_history') }}: {{ $fundName }}
                </td>
            </tr>
            </tbody>
        </table>
    @endif

    <table class="table m-b-xs">
        <tbody>
        <tr style="background-color: #337ab7;color: #FFF;">
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">#</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{__('trans.previous_price')}}</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{__('trans.current_price')}}</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{__('trans.direction')}}</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{__('trans.changed_at')}}</th>
        </tr>
        @forelse($records as $record)
            <tr>
                <td style="text-align: center">{{ $loop->iteration }}</td>
                <td style="text-align: center">{{ $record['previous_price'] ?? '-' }}</td>
                <td style="text-align: center">{{ $record['price'] ?? '' }}</td>
                <td style="text-align: center">
                    @php
                        $direction = $record['direction'] ?? '';
                        if (is_array($direction)) {
                            $directionValue = $direction['value'] ?? $direction['name'] ?? '';
                        } else {
                            $directionValue = $direction;
                        }
                    @endphp
                    @if($directionValue === 'up' || $directionValue === 1)
                        {{ __('trans.up') }} ↑
                    @elseif($directionValue === 'down' || $directionValue === -1)
                        {{ __('trans.down') }} ↓
                    @else
                        {{ __('trans.same') }} →
                    @endif
                </td>
                <td style="text-align: center">{{ isset($record['changed_at']) && $record['changed_at'] ? \Carbon\Carbon::parse($record['changed_at'])->format('Y-m-d H:i:s') : '' }}</td>
            </tr>
        @empty
            <tr>
                <td style="text-align: center" colspan="5">{{__('trans.No data available')}}</td>
            </tr>
        @endforelse
        </tbody>
    </table>
@stop












