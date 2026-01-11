@extends('dashboard.admin.layout.main')

@section('title')
    {{$title = __('trans.child-product.index')}}
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
            <li class="breadcrumb-item">
                 <i class="ti ti-medicine-syrup me-2"></i>
                <a href="{{route('admin.products.index')}}">@lang('trans.product.index')</a>
            </li>
            <li class="breadcrumb-item active"> <i class="ti ti-package me-2"></i> {{$title}}</li>
        </ol>
    </nav>

    {{-- Parent Product Info --}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="mb-1"><i class="ti ti-medicine-syrup me-2"></i>@lang('trans.product.index'): {{ $product->name }}</h5>
                    @if($product->category)
                        <span class="badge bg-label-primary me-2">{{ $product->category->name }}</span>
                    @endif
                    @if($product->brand)
                        <span class="badge bg-label-info">{{ $product->brand->name }}</span>
                    @endif
                </div>
                <a href="{{ route('admin.products.show', $product) }}" class="btn btn-outline-primary">
                    <i class="ti ti-eye me-1"></i> @lang('trans.view_details')
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">

            <x-admin.buttons
                extrabuttons="true"
                 createPermission="create-child-product"
                :addbutton="route('admin.products.child-products.create', $product)"
                 deletePermission="delete-child-product"
                 :deletebutton="route('admin.products.child-products.destroy-multiple', $product)"
            >
                <x-slot name="extrabuttonsdiv">
                    @can('create-export')
                        <x-admin.export-button 
                            :route="route('admin.child-product-export')"
                            buttonId="exportChildProductBtn"
                            buttonClass="btn btn-outline-success waves-effect extrabuttonsdiv me-2"
                        />
                    @endcan
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

@endsection

@push('js_files')

    @include('dashboard.shared.deleteAll')
    @include('dashboard.shared.deleteOne')
    @include('dashboard.shared.filter_js' , [ 'index_route' => route('admin.products.child-products.index', $product) ])
@endpush
