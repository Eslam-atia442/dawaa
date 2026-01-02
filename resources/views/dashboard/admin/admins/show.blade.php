@extends('dashboard.admin.layout.main')

@section('title')
    {{$title = __('trans.admin.show')}}
@endsection

@push('css_files')
    <link rel="stylesheet" href="{{asset('assets/validation/form-validation.css')}}">
@endpush

@section('content')

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <i class="ti ti-home-bolt me-2"></i>
                 <a href="{{route('admin.home')}}">@lang('trans.home')</a>
            </li>

            <li class="breadcrumb-item">
                <a href="{{route('admin.admins.index')}}">@lang('trans.admins')</a>
            </li>

            <li class="breadcrumb-item active"><i class=" menu-icon ti ti-user-bolt"></i> {{$title}}</li>
        </ol>
    </nav>
    <!-- Navbar pills -->
    <div class="row">
        <div class="col-md-12">
            <ul class="nav nav-pills flex-column flex-sm-row mb-4">

                <li class="nav-item">
                    <a class="nav-link tabs active" id="profile-tab"><i
                            class="ti-xs ti ti-user-check me-1"></i> {{__('trans.profile')}}</a>
                </li>

            </ul>
        </div>
    </div>
    <!--/ Navbar pills -->

    <div class="row">

        <div class="col-xl-6 col-lg-6 col-md-5 profile-tab tabs_divs">
            <div class="card mb-4">
                <div class="card-body">
                    <small class="card-text text-uppercase">{{__('trans.user_breif')}}</small>
                    <ul class="list-unstyled mb-4 mt-3">
                        <li class="d-flex align-items-center mb-3">
                            <i class="ti ti-user text-heading"></i
                            ><span class="fw-medium mx-2 text-heading">{{__('trans.name')}} : </span>
                            <span>{{$row->name}}</span>
                        </li>
                        <li class="d-flex align-items-center mb-3">
                            <i class="ti ti-check {{$row->is_blocked ? 'text-danger' : 'text-success'}} text-success"></i
                            ><span class="fw-medium mx-2 text-heading">{{__('trans.is_blocked')}}:</span> <span
                                class="{{$row->is_blocked ? 'text-danger' : 'text-success'}}">
                    {{$row->is_blocked ? __('trans.blocked') : __('trans.unblocked')}}
                </span>
                        </li>
                    </ul>
                    <small class="card-text text-uppercase">{{__('trans.contact_info')}}</small>
                    <ul class="list-unstyled mb-4 mt-3">
                        <li class="d-flex align-items-center mb-3">
                            <i class="ti ti-phone-call"></i><span class="fw-medium mx-2 text-heading">{{__('trans.phone_number')}} : </span>
                            <span>{{$row->phone}}</span>
                        </li>
                        <li class="d-flex align-items-center mb-3">
                            <i class="ti ti-mail"></i><span
                                class="fw-medium mx-2 text-heading">{{__('trans.email')}}:</span>
                            <span>{{$row->email}}</span>
                        </li>
                    </ul>

                </div>
            </div>
        </div>

    </div>

@endsection

@push('js_files')
    @include('dashboard.shared.submitAddForm')
    @include('dashboard.shared.addImage')
@endpush
