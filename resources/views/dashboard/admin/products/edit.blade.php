@extends('dashboard.admin.layout.main')

@section('title')
    {{$title = __('trans.product.edit')}}
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
                <i class="ti ti-medicine-syrup me-2"></i>
                <a href="{{route('admin.products.index')}}">@lang('trans.product.index')</a>
            </li>
            <li class="breadcrumb-item active"> <i class="ti ti-file-pencil me-2"></i> {{$title}}</li>
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
                    <form class="form validated-form" method="POST" action="{{route('admin.products.update' , ['product' => $row->id])}}" novalidate enctype="multipart/form-data">
                        @csrf
                        <div class="tab-content" id="v-pills-tabContent">
                            <div class="tab-pane fade show active" id="v-pills-general" role="tabpanel" aria-labelledby="v-pills-general-tab" tabindex="0">
                                <div class="row g-3">
                                    @foreach (languages() as $lang)
                                        <x-admin.input
                                            required="true"
                                            :value="$row->getTranslation('name', $lang)"
                                            name="name[{{$lang}}]"
                                            label="name_{{$lang}}"
                                            type="text"
                                            col="col-xl-6"
                                            placeholder="name_{{$lang}}"
                                        />
                                    @endforeach
                                    @foreach (languages() as $lang)
                                        <x-admin.input
                                            :value="$row->getTranslation('description', $lang)"
                                            name="description[{{$lang}}]"
                                            label="description_{{$lang}}"
                                            type="textarea"
                                            col="col-xl-6"
                                            placeholder="description_{{$lang}}"
                                            rows="4"
                                        />
                                    @endforeach
                                    <x-admin.input
                                        name="store_id"
                                        label="store.index"
                                        type="select"
                                        col="col-xl-6"
                                        :value="$row->store_id"
                                        :options="$stores->map(function($store) { return ['id' => $store->id, 'name' => $store->name]; })->toArray()"
                                    />
                                    <x-admin.input
                                        name="category_id"
                                        label="category.index"
                                        type="select"
                                        col="col-xl-6"
                                        :value="$row->category_id"
                                        :options="$categories->map(function($category) { return ['id' => $category->id, 'name' => $category->name]; })->toArray()"
                                    />
                                    <x-admin.input
                                        name="brand_id"
                                        label="brand.index"
                                        type="select"
                                        col="col-xl-6"
                                        :value="$row->brand_id"
                                        :options="$brands->map(function($brand) { return ['id' => $brand->id, 'name' => $brand->name]; })->toArray()"
                                    />
                                    <x-admin.input
                                        name="is_active"
                                        label="is_active"
                                        type="checkbox"
                                        col="col-xl-6"
                                        :checked="$row->is_active"
                                    />
                                    <x-admin.input
                                        name="has_discount"
                                        label="has_discount"
                                        type="checkbox"
                                        col="col-xl-6"
                                        id="has_discount"
                                        :checked="$row->has_discount ?? false"
                                    />
                                    <x-admin.input
                                        name="discount_percentage"
                                        label="discount_percentage"
                                        type="number"
                                        col="col-xl-6"
                                        :value="$row->discount_percentage"
                                        step="0.01"
                                        min="0"
                                        max="100"
                                        placeholder="0.00"
                                    />
                                    <x-admin.input
                                        name="minimum_quantity"
                                        label="minimum_quantity"
                                        type="number"
                                        col="col-xl-6"
                                        :value="$row->minimum_quantity"
                                        placeholder="minimum_quantity"
                                    />
                                </div>
                            </div>
                            <div class="tab-pane fade" id="v-pills-media" role="tabpanel" aria-labelledby="v-pills-media-tab" tabindex="0">
                                <div class="row g-3">
                                    {{-- Example file input, uncomment and adjust as needed --}}
                                   
                                    <x-admin.file
                                        :files="$row->getMedia('image')"
                                        name="image"
                                        class="col-6"
                                        :multiple="false"
                                        accept="image/*"
                                    />
                                    <x-admin.file
                                        :files="$row->getMedia('gallery')"
                                        name="gallery"
                                        class="col-6"
                                        :multiple="true"
                                        accept="image/*"
                                    />
                                     
                                </div>
                            </div>
                        </div>
                        <div class="pt-4 d-flex justify-content-center mt-3">
                            <button type="submit" class="btn btn-primary me-sm-3 me-1 waves-effect waves-light submit-button">@lang('trans.edit')</button>
                            <a class="btn btn-label-dribbble waves-effect" href="{{ url()->previous()}}">@lang('trans.back')</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js_files')
    @include('dashboard.shared.submitEditForm')
    @include('dashboard.shared.addImage')
    <script>
        $(document).ready(function() {
            var hasDiscountCheckbox = $('#has_discount');
            var discountPercentageField = $('#discount_percentage').closest('.form-group');
            
            function toggleDiscountField() {
                if (hasDiscountCheckbox.is(':checked')) {
                    discountPercentageField.show();
                    $('#discount_percentage').prop('required', true);
                } else {
                    discountPercentageField.hide();
                    $('#discount_percentage').prop('required', false);
                    $('#discount_percentage').val('');
                }
            }
            
            hasDiscountCheckbox.on('change', toggleDiscountField);
            toggleDiscountField();
        });
    </script>
@endpush
