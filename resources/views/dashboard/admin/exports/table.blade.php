<table class="datatables-products table">

    <thead class="border-top">
    <tr>
        <th class="dt-checkboxes-cell">
            <input type="checkbox" class="dt-checkboxes form-check-input" id="checkedAll">
        </th>

        <th>{{ __('trans.name') }}</th>
        <th>{{ __('trans.model') }}</th>
        <th>{{ __('trans.status') }}</th>
        <th>{{ __('trans.records') }}</th>
        <th>{{ __('trans.created_at') }}</th>
        <th>{{ __('trans.completed_at') }}</th>
        <th>{{ __('trans.actions') }}</th>
    </tr>
    </thead>
    <tbody>
    @forelse ($rows ?? [] as $row)
        <tr class="delete_row">
            <td class="dt-checkboxes-cell">
                <input type="checkbox" class="dt-checkboxes checkSingle form-check-input" id="{{ $row->id }}">
            </td>

            <td>
                <span class="text-truncate d-flex align-items-center">{{ $row->name }}</span>
            </td>

            <td>
                <span class="text-truncate d-flex align-items-center">{{ $row->model }}</span>
            </td>

            <td>
                <span class="badge bg-{{ $row->status_color }}">{{ $row->status_label }}</span>
            </td>

            <td>
                <span class="text-truncate d-flex align-items-center">
                    {{ $row->total_records ?: '-' }}
                </span>
            </td>

            <td>
                <span class="text-truncate d-flex align-items-center">
                    {{ $row->created_at->format('Y-m-d H:i') }}
                </span>
            </td>

            <td>
                <span class="text-truncate d-flex align-items-center">
                    {{ $row->completed_at ? $row->completed_at->format('Y-m-d H:i') : '-' }}
                </span>
            </td>

            <td>
                <div class="d-inline-block text-nowrap">
                    @if($row->isReady())
                        <button onclick="downloadExport({{ $row->id }})"
                                class="btn btn-sm btn-icon btn-success"
                                title="{{ __('trans.download') }}">
                            <i class="ti ti-download"></i>
                        </button>
                    @endif

                    @if($row->isFailed())
                        <span class="text-danger small" title="{{ $row->error_message }}">
                            <i class="ti ti-alert-circle"></i>
                        </span>
                    @endif

                    <button onclick="deleteExport({{ $row->id }})"
                            class="btn btn-sm btn-icon btn-danger"
                            title="{{ __('trans.delete') }}">
                        <i class="ti ti-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="8" class="text-center py-4">
                <div class="d-flex flex-column align-items-center">
                    <i class="ti ti-file-x text-muted mb-2" style="font-size: 2rem;"></i>
                    <span class="text-muted">{{ __('trans.no_exports_found') }}</span>
                </div>
            </td>
        </tr>
    @endforelse
    </tbody>
</table>
