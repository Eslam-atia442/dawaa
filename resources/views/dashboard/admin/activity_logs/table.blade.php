<table class="datatables-products table">

    <thead class="border-top">
    <tr>
        </th>
        <th>{{ __('trans.id') }}</th>
        <th>{{ __('trans.by') }}</th>
        <th>{{ __('trans.subject_type') }}</th>
        <th>{{ __('trans.subject_id') }}</th>
        <th>{{ __('trans.actions') }}</th>
        <th>{{ __('trans.computer_name') }}</th>
        <th>{{ __('trans.ip') }}</th>
        <th>{{ __('trans.Physical Address') }}</th>
        <th>{{ __('trans.created_at') }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($rows as $row)
        <tr class="delete_row">


            <td>
                <span class="text-truncate d-flex align-items-center">{{ $row->id}}</span>
            </td>

            <td>
                <span class="text-truncate d-flex align-items-center">{{ @$row->actor->name}}</span>
            </td>
            <td>
                <span class="text-truncate d-flex align-items-center">{{ @$row->subject_type}}</span>
            </td>
            <td>
                <span class="text-truncate d-flex align-items-center">{{ @$row->subject_id}}</span>
            </td>

            <td>
                <span class="text-truncate d-flex align-items-center">{{ @$row->getExtraProperty('action')}}</span>
            </td>
            <td>
                <span
                    class="text-truncate d-flex align-items-center">{{ @$row->getExtraProperty('computer_name')}}</span>
            </td>
            <td>
                <span
                    class="text-truncate d-flex align-items-center">{{ $row->getExtraProperty('ip')}}</span>
            </td>
            <td>
                <span
                    class="text-truncate d-flex align-items-center">{{ $row->getExtraProperty('Physical Address')}}</span>
            </td>

            <td>
                <span class="text-truncate d-flex align-items-center">{{ $row->created_at}}</span>
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
