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
            @php
                $userName = data_get($record, 'user.name');
                if (is_array($userName)) {
                    $locale = app()->getLocale();
                    $userName = $userName[$locale] ?? $userName['en'] ?? head($userName) ?? '-';
                }
                $userName = $userName ?? '-';
                $paymentType = data_get($record, 'payment_type');
                $paymentLabel = $paymentType !== null && $paymentType !== '' ? (\App\Enums\PaymentTypeEnum::tryFrom($paymentType)?->label() ?? $paymentType) : '';
            @endphp
            <tr>
                <td style="text-align: center">{{ $loop->iteration }}</td>
                <td style="text-align: center">{{ data_get($record, 'id') }}</td>
                <td style="text-align: center">{{ $userName }}</td>
                <td style="text-align: center">{{ number_format((float) (data_get($record, 'total_price') ?? 0), 2) }}</td>
                <td style="text-align: center">{{ $paymentLabel }}</td>
                <td style="text-align: center">{{ data_get($record, 'parent_id') ? __('trans.refund_order') : __('trans.order.index') }}</td>
                <td style="text-align: center">{{ data_get($record, 'created_at') ? \Carbon\Carbon::parse(data_get($record, 'created_at'))->format('Y-m-d H:i:s') : '' }}</td>
            </tr>
        @empty
            <tr>
                <td style="text-align: center" colspan="7">{{ __('trans.No data available') }}</td>
            </tr>
        @endforelse
        </tbody>
    </table>
@stop
