@extends('dashboard.admin.layout.main')

@section('title')
    {{$title = __('trans.country.index')}}
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
            <li class="breadcrumb-item active"> <i class="ti ti-building-skyscraper me-2"></i> {{$title}}</li>
        </ol>
    </nav>

    <div class="card">
        <div class="card-header">

            <x-admin.buttons
                extrabuttons="true"
                 createPermission="create-country"
                :addbutton="route('admin.countries.create')"
                 deletePermission="delete-country"
                 :deletebutton="route('admin.countries.destroy-multiple')"
            >
                <x-slot name="extrabuttonsdiv">
                    {{--    <a class=" me-1 btn btn-outline-info waves-effect extrabuttonsdiv "
                       href="{{route('admin.master-export', 'Country')}}"><i class="fa fa-file-excel-o"></i>
                        {{ __('trans.export_excel') }}</a> --}}
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
    @include('dashboard.shared.filter_js' , [ 'index_route' => route('admin.countries.index') ])
@endpush
