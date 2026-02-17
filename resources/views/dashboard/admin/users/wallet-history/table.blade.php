<table class="datatables-wallet-history table">

    <thead class="border-top">
    <tr>
        <th>@lang('trans.id')</th>
        <th>@lang('trans.type')</th>
        <th>@lang('trans.amount')</th>
        <th>@lang('trans.balance_before')</th>
        <th>@lang('trans.balance_after')</th>
        <th>@lang('trans.reference')</th>
        <th>@lang('trans.description')</th>
        <th>@lang('trans.admin.index')</th>
        <th>@lang('trans.created_at')</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($rows as $row)
        <tr>
            <td>{{ $row->id }}</td>
            <td>
                <span class="badge bg-{{ $row->type == App\Enums\WalletTransactionTypeEnum::add->value ? 'success' : 'danger' }}">
                    {{ $row->type == App\Enums\WalletTransactionTypeEnum::add->value ? __('trans.credit') : __('trans.debit') }} 
                </span>
            </td>
            <td class="{{ $row->type === App\Enums\WalletTransactionTypeEnum::add->value ? 'text-success' : 'text-danger' }}">
                {{ $row->type === App\Enums\WalletTransactionTypeEnum::add->value ? '+' : '-' }}{{ number_format($row->amount, 2) }}
            </td>
            <td>{{ number_format($row->balance_before ?? 0, 2) }}</td>
            <td>{{ number_format($row->balance_after ?? 0, 2) }}</td>
            <td>
                @if($row->reference_type && $row->reference_id)
                    {{ ucfirst(class_basename($row->reference_type)) }} #{{ $row->reference_id }}
                @else
                    -
                @endif
            </td>
            <td><span class="text-truncate d-inline-block" style="max-width: 200px;" title="{{ $row->description }}">{{ $row->description ?? '-' }}</span></td>
            <td>{{ $row->admin?->name ?? '-' }}</td>
            <td>{{ $row->created_at?->format('Y-m-d H:i:s') }}</td>
        </tr>
    @endforeach
    </tbody>

</table>

@if ($rows->count() == 0)
    <div class="d-flex flex-column w-100 mt-4 mb-4 align-items-center">
        <img src="{{asset('/storage/images/no_data.png')}}" width="200px" style="" alt="">
        <span class="mt-2" style="font-family: cairo ;margin-right: 35px">{{__('trans.there_are_no_matches_matching')}}</span>
    </div>
@endif

@if ($rows->count() > 0 && $rows instanceof \Illuminate\Pagination\AbstractPaginator )
    <div class="d-flex justify-content-center mt-3">
        {{$rows->links('pagination::bootstrap-4')}}
    </div>
@endif

@include('dashboard.shared.table-loader')
