<table class="datatables-best-sellers table">

    <thead class="border-top">
    <tr>
        <th>#</th>
        <th>{{ __('trans.product_name') }}</th>
        <th>{{ __('trans.total_quantity_sold') }}</th>
        <th>{{ __('trans.orders_count') }}</th>
        <th>{{ __('trans.total_revenue') }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($rows as $row)
        <tr>
            <td>
                <span class="text-truncate d-flex align-items-center">{{ $loop->iteration + ($rows->currentPage() - 1) * $rows->perPage() }}</span>
            </td>
            <td>
                <span class="text-truncate d-flex align-items-center">{{ $row->product->name ?? '-' }}</span>
            </td>
            <td>
                <span class="text-truncate d-flex align-items-center">{{ $row->total_quantity_sold }}</span>
            </td>
            <td>
                <span class="text-truncate d-flex align-items-center">{{ $row->orders_count }}</span>
            </td>
            <td>
                <span class="text-truncate d-flex align-items-center">{{ number_format($row->total_revenue, 2) }}</span>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

{{-- no data found div --}}
@if ($rows->count() == 0)
    <div class="text-center py-5">
        <p class="mb-0">{{ __('trans.no_data_found') }}</p>
    </div>
@endif

{{-- pagination links div --}}
@if ($rows->count() > 0 && $rows instanceof \Illuminate\Pagination\AbstractPaginator )
    <div class="d-flex justify-content-center mt-3">
        {{$rows->links('pagination::bootstrap-4')}}
    </div>
@endif

@include('dashboard.shared.table-loader')
