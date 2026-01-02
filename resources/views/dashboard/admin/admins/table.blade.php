<table class="datatables-products table">

    <thead class="border-top">
    <tr>
        <th class="dt-checkboxes-cell"><input type="checkbox" class="dt-checkboxes form-check-input" id="checkedAll">
        </th>

        <th>{{ __('trans.name') }}</th>
        <th>{{ __('trans.role.index') }}</th>
        <th>{{ __('trans.active') }}</th>
        <th>{{ __('trans.block') }}</th>
        <th>{{ __('trans.actions') }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($rows as $row)
        <tr class="delete_row">
            <td class="dt-checkboxes-cell"><input type="checkbox" class="dt-checkboxes checkSingle form-check-input"
                                                  id="{{ $row->id }}"></td>
            <td>
                <span class="text-truncate d-flex align-items-center">{{ @$row->name}}</span>
            </td>

            <td>
                <span class="text-truncate d-flex align-items-center">{{ @$row->roles[0]->name}}</span>
            </td>


            <td>
                <x-admin.toggle
                    checked="{{$row->is_active}}"
                    url="{{ route('admin.admins-toggle',['admin' => $row->id ,'key' => 'is_active']) }}">
                </x-admin.toggle>
            </td>

            <td>

                <x-admin.toggle
                    checked="{{$row->is_blocked}}"
                    url="{{ route('admin.admins-toggle',['admin' => $row->id ,'key' => 'is_blocked']) }}">
                </x-admin.toggle>

            </td>

            <td>
                <div class="d-inline-block text-nowrap">
                    <a href="{{ route('admin.admins.edit', ['admin' => $row->id]) }}" class="btn btn-sm btn-icon"><i
                            class="text-primary ti ti-edit"></i></a>
                    <a href="{{ route('admin.admins.show' , ['admin' => $row->id])  }}" class="btn btn-sm btn-icon"><i
                            class="text-info ti ti-eye-check"></i></a>
                    <a class="btn btn-sm btn-icon delete-row" data-url="{{ route('admin.admins.destroy', $row->id)  }}"><i
                            class="text-danger ti ti-trash-x"></i></a>

                {{--    <a class="btn btn-sm btn-icon notify" data-bs-toggle="modal" data-bs-target="#notify"
                       data-url="{{ url('admins/admins/notify') }}" data-id="{{ $row->id }}"><i
                            class="text-success ti ti-bell-star"></i></a>
                    <a class="btn btn-sm btn-icon mail" data-bs-toggle="modal" data-bs-target="#mail"
                       data-url="{{ url('admins/admins/notify') }}" data-id="{{ $row->id }}"><i
                            class="text-success ti ti-mail-share"></i></a>--}}


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
