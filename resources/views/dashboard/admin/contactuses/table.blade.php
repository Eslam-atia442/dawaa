<table class="datatables-contactuses table">
    <thead class="border-top">
        <tr>
            <th class="dt-checkboxes-cell"><input type="checkbox" class="dt-checkboxes form-check-input" id="checkedAll"></th>
            <th>{{ __('trans.id') }}</th>
            <th>{{ __('trans.name') }}</th>
            <th>{{ __('trans.email') }}</th>
            <th>{{ __('trans.phone') }}</th>
            <th>{{ __('trans.country.index') }}</th>
            <th>{{ __('trans.created_at') }}</th>
            <th>{{ __('trans.actions') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rows as $row)
            <tr class="delete_row">
                <td class="dt-checkboxes-cell"><input type="checkbox" class="dt-checkboxes checkSingle form-check-input" id="{{ $row->id }}"></td>
                <td><span class="text-truncate d-flex align-items-center">{{ $row->id }}</span></td>
                <td><span class="text-truncate d-flex align-items-center">{{ $row->name ?? '-' }}</span></td>
                <td><span class="text-truncate d-flex align-items-center">{{ $row->email ?? '-' }}</span></td>
                <td><span class="text-truncate d-flex align-items-center">{{ $row->phone ?? '-' }}</span></td>
                <td><span class="text-truncate d-flex align-items-center">{{ $row->country?->name ?? '-' }}</span></td>
                <td><span class="text-truncate d-flex align-items-center">{{ $row->created_at?->format('Y-m-d H:i:s') }}</span></td>
                <td>
                    <div class="d-inline-block text-nowrap">
                        @can('update-contact-us')
                            <a href="{{ route('admin.contactuses.edit', ['contactUs' => $row->id]) }}" class="btn btn-sm btn-icon"><i class="text-primary ti ti-edit"></i></a>
                        @endcan
                        <a href="{{ route('admin.contactuses.show', ['contactUs' => $row->id]) }}" class="btn btn-sm btn-icon"><i class="text-info ti ti-eye-check"></i></a>
                        @can('delete-contact-us')
                            <a class="btn btn-sm btn-icon delete-row" data-url="{{ route('admin.contactuses.destroy', $row->id) }}"><i class="text-danger ti ti-trash-x"></i></a>
                        @endcan
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

@if ($rows->count() == 0)
    <div class="d-flex flex-column w-100 mt-4 mb-4 align-items-center">
        <img src="{{ asset('/storage/images/no_data.png') }}" width="200px" alt="">
        <span class="mt-2" style="font-family: cairo; margin-right: 35px">{{ __('trans.there_are_no_matches_matching') }}</span>
    </div>
@endif

@if ($rows->count() > 0 && $rows instanceof \Illuminate\Pagination\AbstractPaginator)
    <div class="d-flex justify-content-center mt-3">
        {{ $rows->links('pagination::bootstrap-4') }}
    </div>
@endif

@include('dashboard.shared.table-loader')
