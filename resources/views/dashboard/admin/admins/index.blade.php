@extends('dashboard.admin.layout.main')

@section('title')
    {{$title = __('trans.admin.index')}}
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
            <li class="breadcrumb-item active"><i class="menu-icon ti ti-user-bolt"></i> {{$title}}</li>
        </ol>
    </nav>
    <div class="card mb-4">
        <div class="card-widget-separator-wrapper">
            <div class="card-body card-widget-separator">
                <div class="row gy-4 gy-sm-1">

                    <div class="col-sm-6 col-lg-3">
                        <div
                            class="d-flex justify-content-between align-items-start card-widget-1 border-end pb-3 pb-sm-0">
                            <div>
                                <h6 class="mb-2">@lang('trans.blocked_admins')</h6>
                                <h4 class="mb-2 blocked_admins_count">{{$blocked_admins}}</h4>
                            </div>
                            <span class="avatar me-sm-4">
                <span class="avatar-initial bg-label-secondary rounded"><i
                        class="ti-md ti ti-user-x text-danger"></i></span>
              </span>
                        </div>
                        <hr class="d-none d-sm-block d-lg-none me-4">
                    </div>

                    <div class="col-sm-6 col-lg-3">
                        <div
                            class="d-flex justify-content-between align-items-start card-widget-1 border-end pb-3 pb-sm-0">
                            <div>
                                <h6 class="mb-2"> @lang('trans.active_admins') </h6>
                                <h4 class="mb-2 active_admins_count">{{$active_admins}}</h4>
                            </div>
                            <span class="avatar me-sm-4">
                <span class="avatar-initial bg-label-secondary rounded"><i
                        class="ti-md ti ti-user-check text-success"></i></span>
              </span>
                        </div>
                        <hr class="d-none d-sm-block d-lg-none me-4">
                    </div>

                    <div class="col-sm-6 col-lg-3">
                        <div
                            class="d-flex justify-content-between align-items-start card-widget-1 border-end pb-3 pb-sm-0">
                            <div>
                                <h6 class="mb-2">@lang('trans.admins_count')</h6>
                                <h4 class="mb-2 admins_count">{{$admins_count}}</h4>
                            </div>
                            <span class="avatar me-sm-4">
                <span class="avatar-initial bg-label-secondary rounded"><i
                        class="ti-md ti ti-users-minus text-secondary"></i></span>
              </span>
                        </div>
                        <hr class="d-none d-sm-block d-lg-none me-4">
                    </div>

                    <div class="col-sm-6 col-lg-3">
                        <div
                            class="d-flex justify-content-between align-items-start card-widget-1 border-end pb-3 pb-sm-0">
                            <div>
                                <h6 class="mb-2">مستخدمين مكتملي البيانات</h6>
                                <h4 class="mb-2">52</h4>
                            </div>
                            <span class="avatar me-sm-4">
                <span class="avatar-initial bg-label-secondary rounded"><i
                        class="ti-md ti ti-users-plus text-warning"></i></span>
              </span>
                        </div>
                        <hr class="d-none d-sm-block d-lg-none me-4">
                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">

            <x-admin.buttons
                extrabuttons="true"
                createPermission="create-admin"
                deletePermission="delete-admin"
                :addbutton="route('admin.admins.create')"
                :deletebutton="route('admin.admins.destroy-multiple')"
            >
                <x-slot name="extrabuttonsdiv">
                    @can('create-export')
                        <x-admin.export-button 
                            :route="route('admin.admin-export')"
                            buttonId="exportAdminsBtn"
                            buttonClass="btn btn-outline-success waves-effect extrabuttonsdiv me-2"
                        />
                    @endcan

                    <!-- Bulk Action Buttons -->
                    <button type="button" class="me-1 btn btn-outline-success waves-effect bulk-action"
                            data-action="activate" data-url="{{ route('admin.admins.bulk-action') }}">
                        <span class="ti-xs ti ti-user-check me-1"></span>{{ __('trans.bulk_activate') }}
                    </button>

                    <button type="button" class="me-1 btn btn-outline-warning waves-effect bulk-action"
                            data-action="deactivate" data-url="{{ route('admin.admins.bulk-action') }}">
                        <span class="ti-xs ti ti-user-x me-1"></span>{{ __('trans.bulk_deactivate') }}
                    </button>

                    <button type="button" class="me-1 btn btn-outline-danger waves-effect bulk-action"
                            data-action="block" data-url="{{ route('admin.admins.bulk-action') }}">
                        <span class="ti-xs ti ti-user-minus me-1"></span>{{ __('trans.bulk_block') }}
                    </button>

                    <button type="button" class="me-1 btn btn-outline-primary waves-effect bulk-action"
                            data-action="unblock" data-url="{{ route('admin.admins.bulk-action') }}">
                        <span class="ti-xs ti ti-user-plus me-1"></span>{{ __('trans.bulk_unblock') }}
                    </button>

                 {{--   <button type="button"
                            class="btn btn-primary waves-effect waves-light"
                            data-bs-toggle="modal"
                            data-bs-target="#mail"
                    >

                        {{ __('trans.send_email') }}
                    </button>


                    <button type="button" data-bs-toggle="modal" data-bs-target="#mail" data-id="all"
                            class="me-1 btn btn-outline-info waves-effect mail">
                        <span class="ti-xs ti ti-bell-plus me-1"></span>
                    </button>
--}}
                </x-slot>
            </x-admin.buttons>

            <x-admin.filter
                datefilter="true"
                order="true"
                :searchArray="[
                'keyword' => [
                    'input_type' => 'text' ,
                    'input_name' => __('trans.keyword') ,
                ] ,
            ]"
            />
        </div>
        <div class="card-datatable table-responsive table_content_append">
            <div class="card-datatable table-responsive table_content_append">

            </div>
        </div>
    </div>

    <x-admin.NotifyAll
        route="{{  route('admin.send-general-notification',['driver' => \App\Enums\MailDriverEnum::admin->value])}}"/>

@endsection
@push('js_files')
    @include('dashboard.shared.deleteAll')
    @include('dashboard.shared.deleteOne')
    @include('dashboard.shared.filter_js' , [ 'index_route' => route('admin.admins.index') ])
    @include('dashboard.shared.notify')

    <script>
        $(document).ready(function() {
            // Bulk action functionality
            $('.bulk-action').on('click', function() {
                const action = $(this).data('action');
                const url = $(this).data('url');
                const checkedIds = [];

                // Get all checked checkboxes
                $('.checkSingle:checked').each(function() {
                    checkedIds.push($(this).attr('id'));
                });

                if (checkedIds.length === 0) {
                    Swal.fire({
                        title: '{{ __("trans.error") }}!',
                        text: '{{ __("trans.please_select_at_least_one_item") }}',
                        icon: 'error',
                        confirmButtonText: '{{ __("trans.confirm") }}'
                    });
                    return;
                }

                // Confirm action
                let actionText = '';
                switch(action) {
                    case 'activate':
                        actionText = '{{ __("trans.activate") }}';
                        break;
                    case 'deactivate':
                        actionText = '{{ __("trans.dis_activate") }}';
                        break;
                    case 'block':
                        actionText = '{{ __("trans.block") }}';
                        break;
                    case 'unblock':
                        actionText = '{{ __("trans.unblock") }}';
                        break;
                }

                Swal.fire({
                    title: '{{ __("trans.are_you_sure_bulk_action") }}',
                    text: `{{ __("trans.bulk_action_confirmation", ["action" => ""]) }}`.replace(':action', actionText).replace(':count', checkedIds.length),
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: '{{ __("trans.confirm") }}',
                    cancelButtonText: '{{ __("trans.cancel") }}'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Send AJAX request
                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                action: action,
                                ids: checkedIds
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: '{{ __("trans.success") }}!',
                                    text: response.message || '{{ __("trans.bulk_action_success") }}',
                                    icon: 'success',
                                    confirmButtonText: '{{ __("trans.confirm") }}'
                                }).then(() => {
                                    // Reload table
                                    $('.reloadTable').trigger('click');
                                    // Uncheck all checkboxes
                                    $('.dt-checkboxes').prop('checked', false);
                                    $('.checkSingle').prop('checked', false);
                                });
                            },
                            error: function(xhr) {
                                let errorMessage = '{{ __("trans.error_occurred_during_operation") }}';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }
                                Swal.fire({
                                    title: '{{ __("trans.error") }}!',
                                    text: errorMessage,
                                    icon: 'error',
                                    confirmButtonText: '{{ __("trans.confirm") }}'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
