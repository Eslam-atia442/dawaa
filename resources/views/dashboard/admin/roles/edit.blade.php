@extends('dashboard.admin.layout.main')

@section('title')
    {{$title = __('trans.role.edit')}}
@endsection



@section('content')

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <i class="ti ti-home-bolt me-2"></i>
                 <a href="{{route('admin.home')}}">@lang('trans.home')</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{route('admin.roles.index')}}">@lang('trans.role.index')</a>
            </li>
            <li class="breadcrumb-item active"><i class="ti ti-fingerprint"></i>{{$title}}</li>
        </ol>
    </nav>

    <div class="card mb-4 mt-4">
        <div class="row g-0">
            <div class="col-md-3 border-end">
                <div class="nav flex-column nav-pills gap-2 py-4" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <button class="nav-link active d-flex align-items-center" id="v-pills-general-tab" data-bs-toggle="pill" data-bs-target="#v-pills-general" type="button" role="tab" aria-controls="v-pills-general" aria-selected="true">
                        <i class="ti ti-settings me-2"></i> @lang('trans.general')
                    </button>
                </div>
            </div>
            <div class="col-md-9">
                <div class="card-body p-4">
                    <form class="form validated-form" method="POST" action="{{route('admin.roles.update' , ['role' => $row->id])}}" novalidate enctype="multipart/form-data">
                        @csrf
                        <div class="tab-content" id="v-pills-tabContent">
                            <div class="tab-pane fade show active" id="v-pills-general" role="tabpanel" aria-labelledby="v-pills-general-tab" tabindex="0">
                                <div class="row g-3">
                                    <x-admin.input
                                        required="true"
                                        :value="$row->name_ar"
                                        name="name_ar"
                                        label="name_ar"
                                        type="text"
                                        col="col-xl-6"
                                        placeholder="name_ar"
                                        required="true"
                                    />
                                    <x-admin.input
                                        required="true"
                                        :value="$row->name"
                                        name="name"
                                        label="name"
                                        type="text"
                                        col="col-xl-6"
                                        placeholder="name"
                                        required="true"
                                        />

                                    <!-- Master Select All Switch -->
                                    <div class="col-12">
                                        <div class="card border-primary">
                                            <div class="card-header d-flex justify-content-around mt-2">
                                                <div class="">
                                                    <h6 class="mb-0 fw-bold">@lang('trans.select_all_permissions', ['default' => 'Select All Permissions'])</h6>
                                                </div>
                                                <div class="switch-container">
                                                    <label class="switch switch-success">
                                                        <input id="master-select-all" type="checkbox"
                                                               class="switch-input">
                                                        <div class="help-block"></div>
                                                        <span class="switch-toggle-slider">
                                                            <span class="switch-on"><i class="ti ti-check"></i></span>
                                                            <span class="switch-off"><i class="ti ti-x"></i></span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @foreach($permissions as $title => $permission)
                                        <div class="col-xl-6 col-md-6 col-lg-6 mb-4 order-2 order-xl-0">
                                                <div class="card h-100">
                                                <div class="card-title px-2 d-flex justify-content-around mt-2"
                                                     style="padding: 15px;">
                                                    <div class="card-title mb-3">
                                                        <h5 class="mb-0">@lang('trans.' .   lcfirst($title).'.index')</h5>
                                                    </div>
                                                    <div class="switch-container" style="flex-shrink: 0;">
                                                        <label class="switch switch-success">
                                                            <input id="{{  lcfirst($title) }}" type="checkbox"
                                                                   class="switch-input category-select-all">
                                                            <div class="help-block"></div>
                                                            <span class="switch-toggle-slider">
                                                                <span class="switch-on"><i class="ti ti-check"></i></span>
                                                                <span class="switch-off"><i class="ti ti-x"></i></span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="card-body" style="padding: 15px;">
                                                    @foreach($permission as $key => $value)
                                                        <ul class="  mb-0">
                                                            <li class="mb-2 d-flex justify-content-between align-items-center   p-1">
                                                                <div  class="col-8">
                                                                    <p class="mb-0">@lang('trans.' .  lcfirst($title).'.'.$value['action'])</p>
                                                                </div>
                                                                <div class="switch-container col-4">
                                                                    <label class="switch switch-success">
                                                                        <input type="checkbox" name="permissions[]"
                                                                               class="switch-input {{  lcfirst($title) }} permission-item"
                                                                               id="{{ $value['id'] }}" value="{{ $value['id'] }}"
                                                                               data-action="{{$value['action']}}" {{ in_array($value['id'], $row->permissions->pluck('id')->toArray()) ? 'checked' : '' }}>
                                                                        <div class="help-block"></div>
                                                                        <span class="switch-toggle-slider">
                                                                             <span class="switch-on"><i class="ti ti-check"></i></span>
                                                                             <span class="switch-off"><i class="ti ti-x"></i></span>
                                                                        </span>
                                                                    </label>
                                                                </div>
                                                            </li>
                                                        </ul>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="pt-4 d-flex justify-content-center mt-3">
                            <button type="submit" class="btn btn-primary me-sm-3 me-1 waves-effect waves-light submit-button">{{__('trans.update')}}</button>
                            <a class="btn btn-label-dribbble waves-effect" href="{{ url()->previous()}}">{{__('trans.back')}}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js_files')
    @include('dashboard.shared.submitEditForm')
    @include('dashboard.shared.addImage')
    <script>
        $(document).ready(function() {
            // Master Select All functionality
            $('#master-select-all').on('change', function () {
                let isChecked = $(this).is(':checked');

                // Select/deselect all category switches
                $('.category-select-all').prop('checked', isChecked).trigger('change');

                // Select/deselect all individual permission switches
                $('.permission-item').prop('checked', isChecked).trigger('change');
            });

            // When "select all" switch is selected, select all permissions in the corresponding card
            $('.category-select-all').on('change', function () {
                let isChecked = $(this).is(':checked');
                let card = $(this).closest('.card');
                card.find('.permission-item').prop('checked', isChecked).trigger('change');

                // Update master switch based on all category switches
                updateMasterSwitch();
            });

            // When any permission is selected, check if all permissions are selected to activate the "select all" switch
            $('.permission-item').on('change', function () {
                let card = $(this).closest('.card');

                // When any permission in a card where data-action != "read-all" is selected, select the permission with data-action = "read-all"
                if ($(this).data('action') != 'read-all' && $(this).is(':checked')) {
                    card.find('.permission-item[data-action="read-all"]').prop('checked', true);
                }

                // Check if all permissions are selected to activate the "select all" switch
                let allChecked = true;
                card.find('.permission-item').each(function () {
                    if (!$(this).is(':checked')) {
                        allChecked = false;
                    }
                });
                card.find('.category-select-all').prop('checked', allChecked);

                // Update master switch based on all permission switches
                updateMasterSwitch();
            });

            // Function to update master switch state
            function updateMasterSwitch() {
                let allPermissionsChecked = true;

                // Check if all permissions are checked
                $('.permission-item').each(function() {
                    if (!$(this).is(':checked')) {
                        allPermissionsChecked = false;
                    }
                });

                // Update master switch based on all permission items
                $('#master-select-all').prop('checked', allPermissionsChecked);
            }

            // Initial check on page load to activate "select all" switch if all permissions are selected
            $('.card').each(function() {
                let card = $(this);
                let allChecked = true;
                card.find('.permission-item').each(function() {
                    if (!$(this).is(':checked')) {
                        allChecked = false;
                    }
                });
                card.find('.category-select-all').prop('checked', allChecked);
            });

            // Update master switch on page load
            updateMasterSwitch();
        });
    </script>
@endpush
