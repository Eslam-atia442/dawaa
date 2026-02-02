
<table class="datatables-products table">

    <thead class="border-top">
    <tr>
        <th>@lang('trans.id')</th>
        <th>@lang('trans.type')</th>
        <th>@lang('trans.quantity_change')</th>
        <th>@lang('trans.quantity_before')</th>
        <th>@lang('trans.quantity_after')</th>
        <th>@lang('trans.reason')</th>
        <th>@lang('trans.reference')</th>
        <th>@lang('trans.note')</th>
        <th>@lang('trans.admins')</th>
        <th>@lang('trans.created_at')</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($rows as $row)
        <tr class="delete_row">
            <td>{{ $row->id }}</td>
            <td>
                <span class="badge bg-{{ $row->type === 'credit' ? 'success' : 'danger' }}">
                    {{ $row->type_label }}
                </span>
            </td>
            <td class="{{ $row->type === 'credit' ? 'text-success' : 'text-danger' }}">
                {{ $row->type === 'credit' ? '+' : '' }}{{ $row->quantity_change }}
            </td>
            <td>{{ $row->quantity_before }}</td>
            <td>{{ $row->quantity_after }}</td>
            <td>
                <span class="badge bg-secondary">{{ $row->reason_label }}</span>
            </td>
            <td>
                @if($row->reference_type && $row->reference_id)
                    {{ ucfirst($row->reference_type) }} #{{ $row->reference_id }}
                @else
                    -
                @endif
            </td>
            <td>{{ $row->notes ?? '-' }}</td>
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
