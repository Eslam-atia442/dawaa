
<table class="datatables-child-products table">

    <thead class="border-top">
    <tr>
        <th class="dt-checkboxes-cell"><input type="checkbox" class="dt-checkboxes form-check-input" id="checkedAll"></th>

        <th>{{ __('trans.price') }}</th>
        <th>{{ __('trans.quantity') }}</th>
        <th>{{ __('trans.expiry_date') }}</th>
        <th>{{ __('trans.production_line_number') }}</th>
        <th>{{ __('trans.activate') }}</th>
        <th>{{ __('trans.actions') }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($rows as $row)
        <tr class="delete_row">
            <td class="dt-checkboxes-cell"><input type="checkbox" class="dt-checkboxes checkSingle form-check-input" id="{{ $row->id }}"></td>
            <td>
                <span class="text-truncate d-flex align-items-center">{{ number_format($row->price, 2) }}</span>
            </td>
            <td>
                <span class="text-truncate d-flex align-items-center">{{ $row->quantity }}</span>
            </td>
            <td>
                <span class="text-truncate d-flex align-items-center">{{ $row->expiry_date ? \Carbon\Carbon::parse($row->expiry_date)->format('Y-m-d') : '-' }}</span>
            </td>
            <td>
                <span class="text-truncate d-flex align-items-center">{{ $row->production_line_number ?? '-' }}</span>
            </td>
             <td>
                <x-admin.toggle
                    checked="{{$row->is_active}}"
                    url="{{ route('admin.child-product-toggle',['product' => $product->id, 'childProduct' => $row->id ,'key' => 'is_active']) }}">
                </x-admin.toggle>
            </td>

            <td>
                <div class="d-inline-block text-nowrap">
                   @can('update-child-product')
                    <a href="{{ route('admin.products.child-products.edit', ['product' => $product->id, 'childProduct' => $row->id]) }}" class="btn btn-sm btn-icon"><i class="text-primary ti ti-edit"></i></a>
                   @endcan
                    <a href="{{ route('admin.products.child-products.show', ['product' => $product->id, 'childProduct' => $row->id]) }}" class="btn btn-sm btn-icon"><i class="text-info ti ti-eye-check"></i></a>
                   @can('delete-child-product')
                    <a class="btn btn-sm btn-icon delete-row" data-url="{{ route('admin.products.child-products.destroy', ['product' => $product->id, 'childProduct' => $row->id]) }}"><i class="text-danger ti ti-trash-x"></i></a>
                   @endcan
                </div>
            </td>

        </tr>
    @endforeach
    </tbody>

</table>

{{-- no data found div --}}
@if ($rows->count() == 0)
    <div class="d-flex flex-column w-100 mt-4 mb-4 align-items-center">
        <img src="{{asset('/storage/images/no_data.png')}}" width="200px" style="" alt="">
        <span class="mt-2" style="font-family: cairo ;margin-right: 35px">{{__('trans.there_are_no_matches_matching')}}</span>
    </div>
@endif
{{-- no data found div --}}

{{-- pagination  links div --}}
@if ($rows->count() > 0 && $rows instanceof \Illuminate\Pagination\AbstractPaginator )
    <div class="d-flex justify-content-center mt-3">
        {{$rows->links('pagination::bootstrap-4')}}
    </div>
@endif
{{-- pagination  links div --}}

@include('dashboard.shared.table-loader')
