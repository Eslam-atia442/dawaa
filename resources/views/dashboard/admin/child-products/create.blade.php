@extends('dashboard.admin.layout.main')

@section('title')
    {{$title = __('trans.child-product.create')}}
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
            <li class="breadcrumb-item active">  <i class="ti ti-file-plus me-2"></i> {{$title}}</li>
        </ol>
    </nav>

    <div class="card mb-4 mt-4">
        <div class="row g-0">
            <div class="col-md-3 border-end">
                <div class="nav flex-column nav-pills gap-2 py-4" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <button class="nav-link active d-flex align-items-center" id="v-pills-general-tab" data-bs-toggle="pill" data-bs-target="#v-pills-general" type="button" role="tab" aria-controls="v-pills-general" aria-selected="true">
                        <i class="ti ti-settings me-2"></i> @lang('trans.general')
                    </button>
                    <button class="nav-link d-flex align-items-center" id="v-pills-media-tab" data-bs-toggle="pill" data-bs-target="#v-pills-media" type="button" role="tab" aria-controls="v-pills-media" aria-selected="false">
                        <i class="ti ti-photo me-2"></i> @lang('trans.media')
                    </button>
                </div>
            </div>
            <div class="col-md-9">
                <div class="card-body p-4">
                    <form class="form validated-form" method="POST" action="{{route('admin.child-products.store')}}" novalidate enctype="multipart/form-data">
                        @csrf
                        <div class="tab-content" id="v-pills-tabContent">
                            <div class="tab-pane fade show active" id="v-pills-general" role="tabpanel" aria-labelledby="v-pills-general-tab" tabindex="0">
                                <div class="row g-3">
                                    <x-admin.input
                                        required="true"
                                        name="parent_id"
                                        label="product.index"
                                        type="select"
                                        col="col-xl-6"
                                        :value="request('parent_id')"
                                        :options="$products->map(function($product) { return ['id' => $product->id, 'name' => $product->name]; })->toArray()"
                                    />
                                    <x-admin.input
                                        required="true"
                                        name="price"
                                        label="price"
                                        type="number"
                                        col="col-xl-6"
                                        placeholder="price"
                                        step="0.01"
                                        min="0"
                                    />
                                    <x-admin.input
                                        required="true"
                                        name="quantity"
                                        label="quantity"
                                        type="number"
                                        col="col-xl-6"
                                        placeholder="quantity"
                                        min="0"
                                    />
                                    <x-admin.input
                                        name="expiry_date"
                                        label="expiry_date"
                                        type="date"
                                        col="col-xl-6"
                                        placeholder="expiry_date"
                                    />
                                    <x-admin.input
                                        name="production_line_number"
                                        label="production_line_number"
                                        type="text"
                                        col="col-xl-6"
                                        placeholder="production_line_number"
                                    />
                                    <x-admin.input
                                        name="is_active"
                                        label="is_active"
                                        type="checkbox"
                                        col="col-xl-6"
                                        :checked="true"
                                    />
                                </div>
                            </div>
                            <div class="tab-pane fade" id="v-pills-media" role="tabpanel" aria-labelledby="v-pills-media-tab" tabindex="0">
                                <div class="row g-3">
                                    {{-- Example file input, uncomment and adjust as needed --}}
                                    {{--
                                    <x-admin.file
                                        name="image"
                                        class="col-6"
                                        :multiple="false"
                                        accept="image/*"
                                    />
                                    --}}
                                </div>
                            </div>
                        </div>
                        <div class="pt-4 d-flex justify-content-center mt-3">
                            <button type="submit" class="btn btn-primary me-sm-3 me-1 waves-effect waves-light submit-button">{{__('trans.add')}}</button>
                            <a class="btn btn-label-dribbble waves-effect" href="{{ url()->previous()}}">{{__('trans.back')}}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js_files')
    @include('dashboard.shared.submitAddForm')
    @include('dashboard.shared.addImage')
@endpush
