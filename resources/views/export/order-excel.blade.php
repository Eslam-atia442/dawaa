@extends('export.excel_layouts.index-for-excel')
@section('content')

    <table class="table m-b-xs">
        <tbody>
        <tr style="background-color: #337ab7;color: #FFF;">
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">#</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{ __('trans.id') }}</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{ __('trans.user.index') }}</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{ __('trans.total_price') }}</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{ __('trans.payment_type') }}</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{ __('trans.type') }}</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{ __('trans.created_at') }}</th>
        </tr>
        @forelse($records as $record)
            <tr>
                <td style="text-align: center">{{ $loop->iteration }}</td>
                <td style="text-align: center">{{ $record['id'] ?? '' }}</td>
                <td style="text-align: center">{{ isset($record['user']['name']) ? $record['user']['name'] : '-' }}</td>
                <td style="text-align: center">{{ number_format($record['total_price'] ?? 0, 2) }}</td>
                <td style="text-align: center">{{ isset($record['payment_type']) ? (\App\Enums\PaymentTypeEnum::tryFrom($record['payment_type'])?->label() ?? $record['payment_type']) : '' }}</td>
                <td style="text-align: center">{{ !empty($record['parent_id']) ? __('trans.refund_order') : __('trans.order.index') }}</td>
                <td style="text-align: center">{{ isset($record['created_at']) && $record['created_at'] ? \Carbon\Carbon::parse($record['created_at'])->format('Y-m-d H:i:s') : '' }}</td>
            </tr>
        @empty
            <tr>
                <td style="text-align: center" colspan="7">{{ __('trans.No data available') }}</td>
            </tr>
        @endforelse
        </tbody>
    </table>
@stop
