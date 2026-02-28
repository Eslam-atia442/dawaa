@extends('dashboard.admin.layout.main')

@section('title')
    {{ $title = __('trans.contactUs.show') }}
@endsection

@push('css_files')
<link rel="stylesheet" href="{{ asset('assets/validation/form-validation.css') }}">
@endpush

@section('content')

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <i class="ti ti-home-bolt me-2"></i>
                <a href="{{ route('admin.home') }}">@lang('trans.home')</a>
            </li>
            <li class="breadcrumb-item">
                <i class="ti ti-message-chatbot me-2"></i>
                <a href="{{ route('admin.contactuses.index') }}">@lang('trans.contactUs.index')</a>
            </li>
            <li class="breadcrumb-item active"><i class="ti ti-file-database"></i> {{ $title }}</li>
        </ol>
    </nav>

    <div class="card mb-4 mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">@lang('trans.contactUs.details')</h5>
            <a href="{{ route('admin.contactuses.index') }}" class="btn btn-sm btn-label-dribbble waves-effect">
                <i class="ti ti-arrow-left me-1"></i>@lang('trans.back')
            </a>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-xl-6">
                    <label class="form-label fw-bold">@lang('trans.id')</label>
                    <p class="form-control-plaintext">{{ $row->id }}</p>
                </div>
                <div class="col-xl-6">
                    <label class="form-label fw-bold">@lang('trans.name')</label>
                    <p class="form-control-plaintext">{{ $row->name ?? '-' }}</p>
                </div>
                <div class="col-xl-6">
                    <label class="form-label fw-bold">@lang('trans.email')</label>
                    <p class="form-control-plaintext">{{ $row->email ?? '-' }}</p>
                </div>
                <div class="col-xl-6">
                    <label class="form-label fw-bold">@lang('trans.phone')</label>
                    <p class="form-control-plaintext">{{ $row->phone ?? '-' }}</p>
                </div>
                <div class="col-xl-6">
                    <label class="form-label fw-bold">@lang('trans.country.index')</label>
                    <p class="form-control-plaintext">{{ $row->country?->name ?? '-' }}</p>
                </div>
                <div class="col-xl-6">
                    <label class="form-label fw-bold">@lang('trans.created_at')</label>
                    <p class="form-control-plaintext">{{ $row->created_at?->format('Y-m-d H:i:s') }}</p>
                </div>
                <div class="col-12">
                    <label class="form-label fw-bold">@lang('trans.text_of_message')</label>
                    <p class="form-control-plaintext" style="white-space: pre-wrap;">{{ $row->message ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="pt-2 d-flex justify-content-center">
        <a class="btn btn-label-dribbble waves-effect" href="{{ route('admin.contactuses.index') }}">@lang('trans.back')</a>
    </div>

@endsection

@push('js_files')
    @include('dashboard.shared.submitAddForm')
    @include('dashboard.shared.addImage')
@endpush
