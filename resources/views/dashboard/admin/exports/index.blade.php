@extends('dashboard.admin.layout.main')

@section('title')
    {{$title = __('trans.export_management')}}
@endsection

@push('css_files')

@endpush

@section('content')

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <i class="ti ti-home-bolt me-2"></i>
                <a href="{{route('admin.home')}}">@lang('trans.home')</a>
            </li>
            <li class="breadcrumb-item active"> <i class="ti ti-file-export me-2"></i> {{$title}}</li>
        </ol>
    </nav>

    <div class="card">
        <div class="card-header">
            <x-admin.buttons
                extrabuttons="true"
                createPermission="create-export"
                :addbutton="route('admin.exports.create')"
                deletePermission="delete-export"
            >
                <x-slot name="extrabuttonsdiv">
                    <button type="button"
                            class="btn btn-outline-success waves-effect extrabuttonsdiv"
                            onclick="refreshExports()">
                        <i class="ti ti-refresh"></i>
                        {{ __('trans.refresh') }}
                    </button>
                </x-slot>
            </x-admin.buttons>

            <x-admin.filter
                datefilter="true"
                order="true"
                :searchArray="[
                    'keyword' => [
                        'input_type' => 'text',
                        'input_name' => __('trans.keyword'),
                    ],
                    'status' => [
                        'input_type' => 'select',
                        'input_name' => __('trans.status'),
                        'rows' => [['id' => '', 'name' => __('trans.all')], ['id' => 'processing', 'name' => __('trans.Processing')], ['id' => 'ready', 'name' => __('trans.Ready to Download')], ['id' => 'failed', 'name' => __('trans.Failed')]],
                    ],
                    'model' => [
                        'input_type' => 'select',
                        'input_name' => __('trans.model'),
                        'rows' => [['id' => 'GoldFund', 'name' => __('trans.goldFund.index')]],
                    ]
                ]"
            />
        </div>

        <div class="card-datatable table-responsive table_content_append">
        </div>
    </div>
@endsection

@push('js_files')
    @include('dashboard.shared.filter_js', ['index_route' => route('admin.exports.index')])

    <script>
        function refreshExports() {
            getData(searchArray());
        }

        function downloadExport(exportId) {
            window.location.href = `/admin/exports/${exportId}/download`;
        }

        function deleteExport(exportId) {
            Swal.fire({
                title: '{{ __("trans.are_you_sure") }}',
                text: '{{ __("trans.you_will_not_be_able_to_revert_this") }}',
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: '{{ __("trans.yes_delete_it") }}',
                cancelButtonText: '{{ __("trans.no_cancel_it") }}',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/admin/exports/${exportId}`,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    position: 'top-center',
                                    text: '{{ __("trans.the_selected_has_been_successfully_deleted") }}',
                                    showConfirmButton: false,
                                    timer: 2000
                                });
                                getData(searchArray());
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: '{{ __("trans.error") }}',
                                    text: response.error || '{{ __("trans.error") }}',
                                    showConfirmButton: true
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: '{{ __("trans.error") }}',
                                text: xhr.responseJSON?.error || '{{ __("trans.error") }}',
                                showConfirmButton: true
                            });
                        }
                    });
                }
            });
        }
    </script>
@endpush
