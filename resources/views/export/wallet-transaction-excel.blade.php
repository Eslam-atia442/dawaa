@extends('export.excel_layouts.index-for-excel')
@section('content')

    <table class="table m-b-xs">
        <tbody>
        <tr style="background-color: #337ab7;color: #FFF;">
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">#</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{__('trans.type')}}</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{__('trans.amount')}}</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{__('trans.balance_before')}}</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{__('trans.balance_after')}}</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{__('trans.reference')}}</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{__('trans.admin.index')}}</th>
            <th style="text-align: center;background-color: #0a6ebd;color: #ffffff;">{{__('trans.created_at')}}</th>
        </tr>
        @forelse($records as $record)
            <tr>
                <td style="text-align: center">{{ $loop->iteration }}</td>
                <td style="text-align: center">{{ ($record['type'] ?? null) === \App\Enums\WalletTransactionTypeEnum::deduct->value ? __('trans.credit') : __('trans.debit') }}</td>
                <td style="text-align: center">{{ number_format($record['amount'] ?? 0, 2) }}</td>
                <td style="text-align: center">{{ number_format($record['balance_before'] ?? 0, 2) }}</td>
                <td style="text-align: center">{{ number_format($record['balance_after'] ?? 0, 2) }}</td>
                <td style="text-align: center">
                    @if(isset($record['reference_type']) && isset($record['reference_id']))
                        {{ ucfirst($record['reference_type']) }} #{{ $record['reference_id'] }}
                    @else
                        -
                    @endif
                </td>
                <td style="text-align: center">{{ $record['admin']['name'] ?? '-' }}</td>
                <td style="text-align: center">{{ isset($record['created_at']) && $record['created_at'] ? \Carbon\Carbon::parse($record['created_at'])->format('Y-m-d H:i:s') : '' }}</td>
            </tr>
        @empty
            <tr>
                <td style="text-align: center" colspan="8">{{__('trans.No data available')}}</td>
            </tr>
        @endforelse
        </tbody>
    </table>
@stop
