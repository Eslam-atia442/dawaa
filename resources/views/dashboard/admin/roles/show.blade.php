@extends('dashboard.admin.layout.main')

@section('title')
    {{$title = __('trans.role.show')}}
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
                <a href="{{route('admin.roles.index')}}">@lang('trans.role.index')</a>
            </li>

            <li class="breadcrumb-item active"><i class="ti ti-fingerprint"></i>{{$title}}</li>
        </ol>
    </nav>

    <div class="card ">
        <div class="card-body  ">
            <div class="row  ">
                <x-admin.input
                    required="true"
                    disabled="true"
                    :value="$row->name_ar"
                    name="name_ar"
                    label="name_ar"
                    type="text"
                    col="col-xl-6"
                    placeholder="name_ar"
                />
                <x-admin.input
                    required="true"
                    disabled="true"
                    :value="$row->name"
                    name="name_en"
                    label="name_en"
                    type="text"
                    col="col-xl-6"
                    placeholder="name"
                />
                @foreach($permissions as $title => $permission)
                    <div class="col-xl-4 col-lg-6 mb-4 order-2 order-xl-0">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-center">
                                <div class="card-title mb-0 text-center">
                                    <h5 class="mb-0 ">@lang('trans.' .  lcfirst($title).'.index')</h5>
                                </div>
                            </div>
                            <div class="card-body text-center">
                                @foreach($permission as $key => $value)

                                    <ul class="list-unstyled mb-0">
                                        <li class="mb-1 d-flex justify-content-center w-100">
                                            <h5 class="{{ !in_array($value['id'], $role_permissions->pluck('id')->toArray()) ? 'text-danger' : 'text-primary' }}"
                                                id="{{ $value['id'] }}">@lang('trans.' .  lcfirst($title).'.'.$value['action'])</h5>
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
    </div>

@endsection

@push('js_files')
    @include('dashboard.shared.submitAddForm')
    @include('dashboard.shared.addImage')
@endpush
