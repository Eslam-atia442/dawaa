@extends('dashboard.admin.layout.main')

@section('title')
    {{$title = __('trans.brand.index')}}
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
            <li class="breadcrumb-item active"> <i class="ti ti-brand-4chan me-2"></i> {{$title}}</li>
        </ol>
    </nav>

    <div class="card">
        <div class="card-header">

            <x-admin.buttons
                extrabuttons="true"
                 createPermission="create-brand"
                :addbutton="route('admin.brands.create')"
                 deletePermission="delete-brand"
                 :deletebutton="route('admin.brands.destroy-multiple')"
            >
                <x-slot name="extrabuttonsdiv">
                    @can('create-export')
                        <!-- <x-admin.export-button 
                            :route="route('admin.brand-export')"
                            buttonId="exportBrandBtn"
                            buttonClass="btn btn-outline-success waves-effect extrabuttonsdiv me-2"
                        /> -->
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
    @include('dashboard.shared.filter_js' , [ 'index_route' => route('admin.brands.index') ])
@endpush
