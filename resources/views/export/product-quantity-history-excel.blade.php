@extends('export.excel_layouts.index-for-excel')
@section('content')

    <table class="table m-b-xs">
        <tbody>
        <tr style="background-color: #337ab7;color: #FFF;">
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">#</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{__('trans.type')}}</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{__('trans.quantity_change')}}</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{__('trans.quantity_before')}}</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{__('trans.quantity_after')}}</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{__('trans.reason')}}</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{__('trans.reference')}}</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{__('trans.note')}}</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{__('trans.admins')}}</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{__('trans.created_at')}}</th>
        </tr>
        @forelse($records as $record)
            <tr>
                <td style="text-align: center">{{ $loop->iteration }}</td>
                <td style="text-align: center">{{ ($record['type'] ?? null) === 'credit' ? __('trans.quantity_credit') : __('trans.quantity_debit') }}</td>
                <td style="text-align: center">{{ $record['quantity_change'] ?? 0 }}</td>
                <td style="text-align: center">{{ $record['quantity_before'] ?? 0 }}</td>
                <td style="text-align: center">{{ $record['quantity_after'] ?? 0 }}</td>
                <td style="text-align: center">
                    @php
                        $reason = $record['reason'] ?? '';
                        $reasonLabel = match($reason) {
                            'buy' => __('trans.quantity_buy'),
                            'refund' => __('trans.quantity_refund'),
                            'order' => __('trans.quantity_order'),
                            'adjustment' => __('trans.quantity_adjustment'),
                            'return' => __('trans.quantity_return'),
                            default => $reason
                        };
                    @endphp
                    {{ $reasonLabel }}
                </td>
                <td style="text-align: center">
                    @if(isset($record['reference_type']) && isset($record['reference_id']))
                        {{ ucfirst($record['reference_type']) }} #{{ $record['reference_id'] }}
                    @else
                        -
                    @endif
                </td>
                <td style="text-align: center">{{ $record['notes'] ?? '-' }}</td>
                <td style="text-align: center">{{ $record['admin']['name'] ?? '-' }}</td>
                <td style="text-align: center">{{ isset($record['created_at']) && $record['created_at'] ? \Carbon\Carbon::parse($record['created_at'])->format('Y-m-d H:i:s') : '' }}</td>
            </tr>
        @empty
            <tr>
                <td style="text-align: center" colspan="10">{{__('trans.No data available')}}</td>
            </tr>
        @endforelse
        </tbody>
    </table>
@stop
