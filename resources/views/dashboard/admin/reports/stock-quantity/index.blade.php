@extends('dashboard.admin.layout.main')

@section('title')
{{$title = __('trans.stock_quantity_report')}}
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
            <i class="ti ti-report-analytics me-2"></i>
            @lang('trans.reports')
        </li>
        <li class="breadcrumb-item active"> <i class="ti ti-package me-2"></i> {{$title}}</li>
    </ol>
</nav>

<div class="card">
    <div class="card-header">

        <x-admin.buttons
            extrabuttons="true"
            createPermission=""
            :addbutton="false"
            deletePermission=""
            :deletebutton="false">
            <x-slot name="extrabuttonsdiv">
                @can('create-export')
                <x-admin.export-button
                    :route="route('admin.reports.stock-quantity.export')"
                    buttonId="exportStockQuantityBtn"
                    buttonClass="btn btn-outline-success waves-effect extrabuttonsdiv me-2" />
                @endcan
            </x-slot>
        </x-admin.buttons>

        <x-admin.filter
            order="true"
            :searchArray="[
                'quantity' => [
                    'input_type' => 'number' ,
                    'input_name' => __('trans.quantity') ,
                ] ,
                'keyword' => [
                    'input_type' => 'text' ,
                    'input_name' => __('trans.keyword') ,
                ] ,
            ]" />
    </div>
    <div class="card-datatable table-responsive table_content_append">
    </div>
</div>

@endsection

@push('js_files')
@include('dashboard.shared.filter_js' , [ 'index_route' => route('admin.reports.stock-quantity.index') ])
@endpush
