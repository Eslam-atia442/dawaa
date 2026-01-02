@extends('dashboard.admin.layout.main')

@section('title')
    {{$title = __('trans.user.edit')}}
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
                <i class="ti ti-user me-2"></i>
                <a href="{{route('admin.users.index')}}">@lang('trans.user.index')</a>
            </li>

            <li class="breadcrumb-item active"> <i class="ti ti-file-pencil me-2"></i> {{$title}}</li>
        </ol>
    </nav>

    <div class="card mb-4 mt-4">
        <form class="card-body form validated-form" method="POST" action="{{route('admin.users.update' , ['user' => $row->id])}}" novalidate>
            @csrf
            <div class="row g-3">
                <x-admin.input :value="$row->name" required="true"  name="name" label="name"
                               type="text" col="col-xl-6" placeholder="name"
                />
                <x-admin.input :value="$row->phone" required="true"  name="phone" label="phone"
                               type="text" col="col-xl-6" placeholder="phone"
                />
                <x-admin.input :value="$row->email" required="true"  name="email" label="email"
                               type="email" col="col-xl-6" placeholder="email"
                />
                <x-admin.input   name="password" label="password"
                               type="password" col="col-xl-6" placeholder="password"
                />
                <x-admin.input   name="password_confirmation" label="password_confirmation"
                               type="password" col="col-xl-6" placeholder="password_confirmation"
                />

            </div>
            <div class="pt-4 d-flex justify-content-center mt-3">
                <button type="submit"
                        class="btn btn-primary me-sm-3 me-1 waves-effect waves-light submit-button">@lang('trans.edit')</button>
                <a class="btn btn-label-dribbble waves-effect" href="{{ url()->previous()}}">@lang('trans.back')</a>
            </div>
        </form>
    </div>
    </div>

@endsection

@push('js_files')
    @include('dashboard.shared.submitEditForm')
    @include('dashboard.shared.addImage')
@endpush
