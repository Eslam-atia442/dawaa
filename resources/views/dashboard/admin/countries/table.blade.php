<table class="datatables-products table">

    <thead class="border-top">
    <tr>
        <th class="dt-checkboxes-cell"><input type="checkbox" class="dt-checkboxes form-check-input" id="checkedAll">
        </th>

        <th>{{ __('trans.flag') }}</th>
        <th>{{ __('trans.name') }}</th>
        <th>{{ __('trans.country_code') }}</th>
        <th>{{ __('trans.currency') }}</th>
        <th>{{ __('trans.currency_code') }}</th>
        <th>{{ __('trans.activate') }}</th>
        <th>{{ __('trans.actions') }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($rows as $row)
        <tr class="delete_row">
            <td class="dt-checkboxes-cell"><input type="checkbox" class="dt-checkboxes checkSingle form-check-input"
                                                  id="{{ $row->id }}"></td>
            <td>
                <img class="avatar avatar me-2 rounded-2 bg-label-secondary"
                     src="https://flagsapi.com/{{$row->iso2}}/shiny/64.png"
                     alt="{{ @$row->logo->id}}">
            </td>
            <td>{{$row->name}}</td>
            <td>{{$row->key}}+</td>
            <td>{{$row->currency}}</td>
            <td>{{$row->currency_code}}</td>
            <td>
                <x-admin.toggle
                    checked="{{$row->is_active}}"
                    url="{{ route('admin.country-toggle',['country' => $row->id ,'key' => 'is_active']) }}">
                </x-admin.toggle>
            </td>
            <td>
                <div class="d-inline-block text-nowrap">
                    @can('update-country')
                        <a href="{{ route('admin.countries.edit', ['country' => $row->id]) }}"
                           class="btn btn-sm btn-icon"><i class="text-primary ti ti-edit"></i></a>
                    @endcan
                    <a href="{{ route('admin.countries.show' , ['country' => $row->id])  }}"
                       class="btn btn-sm btn-icon"><i class="text-info ti ti-eye-check"></i></a>
                    @can('delete-country')
                        <a class="btn btn-sm btn-icon delete-row"
                           data-url="{{ route('admin.countries.destroy', $row->id)  }}"><i
                                class="text-danger ti ti-trash-x"></i></a>
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
        <span class="mt-2"
              style="font-family: cairo ;margin-right: 35px">{{__('trans.there_are_no_matches_matching')}}</span>
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
