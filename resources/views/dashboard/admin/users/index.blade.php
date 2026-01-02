@extends('dashboard.admin.layout.main')

@section('title')
    {{$title = __('trans.user.index')}}
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
            <li class="breadcrumb-item active"> <i class="ti ti-user me-2"></i> {{$title}}</li>
        </ol>
    </nav>


    <div class="card">
        <div class="card-header">

            <x-admin.buttons
                extrabuttons="true"
                createPermission="create-user"
                :addbutton="route('admin.users.create')"
                deletePermission="delete-user"
                :deletebutton="route('admin.users.destroy-multiple')"
            >
                <x-slot name="extrabuttonsdiv">
                    @can('create-export')
                        <x-admin.export-button 
                            :route="route('admin.user-export')"
                            buttonId="exportUsersBtn"
                            buttonClass="btn btn-outline-success waves-effect extrabuttonsdiv me-2"
                        />
                    @endcan

                      <button type="button"
                              class="btn btn-primary waves-effect waves-light"
                              data-bs-toggle="modal"
                              data-bs-target="#mail"
                      >

                          {{ __('trans.send_email') }}
                      </button>

                      <!-- FCM Notification Button -->
                      <button type="button"
                              class="btn btn-success waves-effect waves-light"
                              onclick="openFCMNotificationModal()"
                      >
                          <i class="ti ti-bell"></i> إرسال إشعار FCM
                      </button>

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
        route="{{  route('admin.send-general-notification',['driver' => \App\Enums\MailDriverEnum::user->value])}}"/>

    <!-- Include FCM Notification Modal -->
    @include('components.fcm-notification-modal')

    <!-- Selected Users Actions -->
    <div class="mt-3" id="selectedUsersActions" style="display: none;">
        <div class="alert alert-info">
            <i class="ti ti-info-circle me-2"></i>
            <span id="selectedCount">0</span> مستخدم محدد
            <button type="button" class="btn btn-success btn-sm ms-2" onclick="sendToSelectedUsers()">
                <i class="ti ti-paper-plane"></i> إرسال إشعار للمحددين
            </button>
        </div>
    </div>

@endsection

@push('js_files')
    @include('dashboard.shared.deleteAll')
    @include('dashboard.shared.deleteOne')
    @include('dashboard.shared.filter_js' , [ 'index_route' => route('admin.users.index') ])
    @include('dashboard.shared.notify')
    
    <!-- Include FCM Notification JavaScript -->
    <script src="{{ asset('assets/js/fcm-notification-modal.js') }}"></script>
    
    <script>
    $(document).ready(function() {
        // Handle checkbox changes for FCM notifications
        $(document).on('change', '.dt-checkboxes', function() {
            updateSelectedCount();
        });
        
        // Handle "Select All" checkbox
        $('#checkedAll').on('change', function() {
            updateSelectedCount();
        });
    });

    function sendToSelectedUsers() {
        const selectedUsers = [];
        $('.dt-checkboxes:checked').each(function() {
            if ($(this).attr('id') !== 'checkedAll') {
                const userId = $(this).attr('id');
                const userName = $(this).closest('tr').find('td:nth-child(2)').text().trim();
                selectedUsers.push({
                    id: userId,
                    name: userName
                });
            }
        });

        if (selectedUsers.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'تحذير!',
                text: 'يرجى تحديد مستخدم واحد على الأقل',
                confirmButtonText: 'موافق',
                confirmButtonColor: '#ffc107'
            });
            return;
        }

        openFCMNotificationModal(selectedUsers);
    }

    function updateSelectedCount() {
        const count = $('.dt-checkboxes:checked').not('#checkedAll').length;
        $('#selectedCount').text(count);
        
        if (count > 0) {
            $('#selectedUsersActions').show();
        } else {
            $('#selectedUsersActions').hide();
        }
    }
    </script>
@endpush