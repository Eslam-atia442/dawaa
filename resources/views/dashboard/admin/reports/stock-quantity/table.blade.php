<table class="datatables-stock-quantity table">

    <thead class="border-top">
    <tr>
        <th>#</th>
        <th>{{ __('trans.child_product_name') }}</th>
        <th>{{ __('trans.parent_product') }}</th>
        <th>{{ __('trans.quantity') }}</th>
        <th>{{ __('trans.price') }}</th>
        <th>{{ __('trans.store_name') }}</th>
        <th>{{ __('trans.expiry_date') }}</th>
        <th>{{ __('trans.production_line_number') }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($rows as $row)
        <tr>
            <td>
                <span class="text-truncate d-flex align-items-center">{{ $loop->iteration + ($rows->currentPage() - 1) * $rows->perPage() }}</span>
            </td>
            <td>
                <span class="text-truncate d-flex align-items-center">{{ $row->name ?? '-' }}</span>
            </td>
            <td>
                <span class="text-truncate d-flex align-items-center">{{ $row->parent->name ?? '-' }}</span>
            </td>
            <td>
                <span class="text-truncate d-flex align-items-center">{{ $row->quantity ?? 0 }}</span>
            </td>
            <td>
                <span class="text-truncate d-flex align-items-center">{{ number_format($row->price, 2) }}</span>
            </td>
            <td>
                <span class="text-truncate d-flex align-items-center">{{ $row->parent->store->name ?? '-' }}</span>
            </td>
            <td>
                <span class="text-truncate d-flex align-items-center">{{ $row->expiry_date ?? '-' }}</span>
            </td>
            <td>
                <span class="text-truncate d-flex align-items-center">{{ $row->production_line_number ?? '-' }}</span>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

{{-- no data found div --}}
@if ($rows->count() == 0)
    <div class="text-center py-5">
        <p class="mb-0">{{ __('trans.no_quantity_filter') }}</p>
    </div>
@endif

{{-- pagination links div --}}
@if ($rows->count() > 0 && $rows instanceof \Illuminate\Pagination\AbstractPaginator )
    <div class="d-flex justify-content-center mt-3">
        {{$rows->links('pagination::bootstrap-4')}}
    </div>
@endif

@include('dashboard.shared.table-loader')
