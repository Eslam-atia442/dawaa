@extends('dashboard.admin.layout.main')

@section('title')
{{$title = __('trans.quantity_history')}}
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
            <a href="{{route('admin.products.index')}}">@lang('trans.product.index')</a>
        </li>

        <li class="breadcrumb-item">
            <i class="ti ti-package me-2"></i>
            <a href="{{route('admin.products.show', $product)}}">{{ $product->name }}</a>
        </li>

        <li class="breadcrumb-item">
            <i class="ti ti-packages me-2"></i>
            <a href="{{route('admin.products.child-products.index', $product)}}">@lang('trans.child-product.index')</a>
        </li>

        <li class="breadcrumb-item">
            <i class="ti ti-package me-2"></i>
            <a href="{{route('admin.products.child-products.show', [$product, $childProduct])}}">{{ $childProduct->name }}</a>
        </li>

        <li class="breadcrumb-item active"> <i class="ti ti-history"></i> {{$title}}</li>
    </ol>
</nav>

<div class="card">
    <div class="card-header">
        <x-admin.buttons
            extrabuttons="true"
            :addbutton="false"
            :deletebutton="false"
        >
            <x-slot name="extrabuttonsdiv">
                @can('create-export')
                    <x-admin.export-button 
                        :route="route('admin.products.child-products.quantity-history-export', [$product, $childProduct])"
                        buttonId="exportQuantityHistoryBtn"
                        buttonClass="btn btn-outline-success waves-effect extrabuttonsdiv me-2"
                    />  
                @endcan

                <a href="{{route('admin.products.child-products.show', [$product, $childProduct])}}" class="btn btn-outline-primary waves-effect extrabuttonsdiv">
                    <i class="ti ti-arrow-left me-1"></i>
                    @lang('trans.back')
                </a>
            </x-slot>
        </x-admin.buttons>

        <x-admin.filter
            datefilter="true"
            order="true"
            :searchArray="[
                'type' => [
                    'input_type' => 'select',
                    'input_name' => __('trans.type'),
                    'rows' => [
                        ['id' => '', 'name' => __('trans.all')],
                        ['id' => 'credit', 'name' => __('trans.quantity_credit')],
                        ['id' => 'debit', 'name' => __('trans.quantity_debit')],
                    ]
                ],
                'reason' => [
                    'input_type' => 'select',
                    'input_name' => __('trans.reason'),
                    'rows' => [
                        ['id' => '', 'name' => __('trans.all')],
                        ['id' => 'buy', 'name' => __('trans.quantity_buy')],
                        ['id' => 'order', 'name' => __('trans.quantity_order')],
                        ['id' => 'refund', 'name' => __('trans.quantity_refund')],
                        ['id' => 'adjustment', 'name' => __('trans.quantity_adjustment')],
                        ['id' => 'return', 'name' => __('trans.quantity_return')],
                    ]
                ],
            ]"
        />
    </div>

    <div class="card-body">
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h4 class="card-title">{{ $childProduct->quantity }}</h4>
                        <p class="card-text mb-0">@lang('trans.quantity')</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-{{ $childProduct->is_active ? 'success' : 'warning' }} text-white">
                    <div class="card-body text-center">
                        <h4 class="card-title">
                            @if($childProduct->is_active)
                                <i class="ti ti-check-circle"></i>
                            @else
                                <i class="ti ti-x-circle"></i>
                            @endif
                        </h4>
                        <p class="card-text mb-0">
                            @lang($childProduct->is_active ? 'trans.active' : 'trans.inactive')
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-datatable table-responsive table_content_append">
    </div>
</div>

@endsection

@push('js_files')
@include('dashboard.shared.deleteAll')
@include('dashboard.shared.deleteOne')
@include('dashboard.shared.filter_js', ['index_route' => route('admin.products.child-products.quantity-history', [$product, $childProduct])])
@endpush
