@extends('dashboard.admin.layout.main')

@section('title')
    {{$title = __('trans.child-product.show')}}
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
                <i class="ti ti-package me-2"></i>
                <a href="{{route('admin.child-products.index')}}">@lang('trans.child-product.index')</a>
            </li>

            <li class="breadcrumb-item active"> <i class="ti ti-file-database"></i> {{$title}}</li>
        </ol>
    </nav>

    <div class="card mb-4 mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">@lang('trans.child-product.details')</h5>
            @if($row->parent)
                <a href="{{ route('admin.products.show', $row->parent_id) }}" class="btn btn-outline-primary">
                    <i class="ti ti-medicine-syrup me-1"></i> @lang('trans.view_parent_product')
                </a>
            @endif
        </div>
        <div class="card-body">
            <div class="row g-3">
                {{-- Parent Product --}}
                <div class="col-xl-6">
                    <label class="form-label fw-bold">@lang('trans.product.index')</label>
                    <p class="form-control-plaintext">
                        @if($row->parent)
                            <a href="{{ route('admin.products.show', $row->parent_id) }}">{{ $row->parent->name }}</a>
                        @else
                            -
                        @endif
                    </p>
                </div>

                {{-- Price --}}
                <div class="col-xl-6">
                    <label class="form-label fw-bold">@lang('trans.price')</label>
                    <p class="form-control-plaintext">{{ number_format($row->price, 2) }}</p>
                </div>

                {{-- Quantity --}}
                <div class="col-xl-6">
                    <label class="form-label fw-bold">@lang('trans.quantity')</label>
                    <p class="form-control-plaintext">{{ $row->quantity }}</p>
                </div>

                {{-- Expiry Date --}}
                <div class="col-xl-6">
                    <label class="form-label fw-bold">@lang('trans.expiry_date')</label>
                    <p class="form-control-plaintext">{{ $row->expiry_date ? \Carbon\Carbon::parse($row->expiry_date)->format('Y-m-d') : '-' }}</p>
                </div>

                {{-- Production Line Number --}}
                <div class="col-xl-6">
                    <label class="form-label fw-bold">@lang('trans.production_line_number')</label>
                    <p class="form-control-plaintext">{{ $row->production_line_number ?? '-' }}</p>
                </div>

                {{-- Status --}}
                <div class="col-xl-6">
                    <label class="form-label fw-bold">@lang('trans.status')</label>
                    <p class="form-control-plaintext">
                        @if($row->is_active)
                            <span class="badge bg-success">@lang('trans.active')</span>
                        @else
                            <span class="badge bg-danger">@lang('trans.inactive')</span>
                        @endif
                    </p>
                </div>

                {{-- Created At --}}
                <div class="col-xl-6">
                    <label class="form-label fw-bold">@lang('trans.created_at')</label>
                    <p class="form-control-plaintext">{{ $row->created_at->format('Y-m-d H:i:s') }}</p>
                </div>

                {{-- Updated At --}}
                <div class="col-xl-6">
                    <label class="form-label fw-bold">@lang('trans.updated_at')</label>
                    <p class="form-control-plaintext">{{ $row->updated_at->format('Y-m-d H:i:s') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Parent Product Details --}}
    @if($row->parent)
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="ti ti-medicine-syrup me-2"></i>@lang('trans.product.details')</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-xl-6">
                    <label class="form-label fw-bold">@lang('trans.name')</label>
                    <p class="form-control-plaintext">{{ $row->parent->name }}</p>
                </div>
                <div class="col-xl-6">
                    <label class="form-label fw-bold">@lang('trans.store.index')</label>
                    <p class="form-control-plaintext">{{ $row->parent->store->name ?? '-' }}</p>
                </div>
                <div class="col-xl-6">
                    <label class="form-label fw-bold">@lang('trans.category.index')</label>
                    <p class="form-control-plaintext">{{ $row->parent->category->name ?? '-' }}</p>
                </div>
                <div class="col-xl-6">
                    <label class="form-label fw-bold">@lang('trans.brand.index')</label>
                    <p class="form-control-plaintext">{{ $row->parent->brand->name ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="pt-2 d-flex justify-content-center">
        <a class="btn btn-label-dribbble waves-effect" href="{{ route('admin.child-products.index') }}">@lang('trans.back')</a>
    </div>

@endsection

@push('js_files')

@endpush
