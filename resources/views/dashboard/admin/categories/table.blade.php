
<table class="datatables-products table">

    <thead class="border-top">
    <tr>
        <th class="dt-checkboxes-cell"><input type="checkbox" class="dt-checkboxes form-check-input" id="checkedAll"></th>

        <th>{{ __('trans.name') }}</th>
        <th>{{ __('trans.activate') }}</th>
        <th>{{ __('trans.actions') }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($rows as $row)
        <tr class="delete_row">
            <td class="dt-checkboxes-cell"><input type="checkbox" class="dt-checkboxes checkSingle form-check-input" id="{{ $row->id }}"></td>
            <td>
                <span class="text-truncate d-flex align-items-center">{{ $row->name }}</span>
            </td>

             <td>
                <x-admin.toggle
                    checked="{{$row->is_active}}"
                    url="{{ route('admin.category-toggle',['category' => $row->id ,'key' => 'is_active']) }}">
                </x-admin.toggle>
            </td>

            <td>
                <div class="d-inline-block text-nowrap">
                   @can('update-category')
                    <a href="{{ route('admin.categories.edit', ['category' => $row->id]) }}" class="btn btn-sm btn-icon"><i class="text-primary ti ti-edit"></i></a>
                   @endcan
                    <a href="{{ route('admin.categories.show' , ['category' => $row->id])  }}" class="btn btn-sm btn-icon"><i class="text-info ti ti-eye-check"></i></a>
                   @can('delete-category')
                    <a class="btn btn-sm btn-icon delete-row" data-url="{{ route('admin.categories.destroy', $row->id)  }}"><i class="text-danger ti ti-trash-x"></i></a>
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
