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
                                        name="country_id"
                                        label="country.index"
                                        type="select"
                                        col="col-xl-6"
                                        id="country_id"
                                        :value="$row->city && $row->city->region ? $row->city->region->country_id : ''"
                                        :options="$countries->map(function($country) { return ['id' => $country->id, 'name' => $country->name]; })->toArray()"
                                    />
                                    <x-admin.input
                                        name="region_id"
                                        label="region.index"
                                        type="select"
                                        col="col-xl-6"
                                        id="region_id"
                                        :value="$row->city ? $row->city->region_id : ''"
                                        :options="$regions->map(function($region) { return ['id' => $region->id, 'name' => $region->name]; })->toArray()"
                                    />
                                    <x-admin.input
                                        name="city_id"
                                        label="city.index"
                                        type="select"
                                        col="col-xl-6"
                                        id="city_id"
                                        :value="$row->city_id"
                                        :options="$cities->map(function($city) { return ['id' => $city->id, 'name' => $city->name]; })->toArray()"
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
            var selectedCountryId = '{{ $row->city && $row->city->region ? $row->city->region->country_id : '' }}';
            var selectedRegionId = '{{ $row->city ? $row->city->region_id : '' }}';
            var selectedCityId = '{{ $row->city_id }}';
            
            // Wait for select2 to be initialized
            setTimeout(function() {
                // Handle country change
                $(document).on('change', '#select2Primary_country_id', function() {
                    var countryId = $(this).val();
                    var regionSelect = $('#select2Primary_region_id');
                    var citySelect = $('#select2Primary_city_id');
                    
                    // Clear region and city dropdowns
                    regionSelect.empty().append('<option value="">{{__('trans.choose')}} {{__('trans.region.index')}}</option>');
                    citySelect.empty().append('<option value="">{{__('trans.choose')}} {{__('trans.city.index')}}</option>');
                    regionSelect.val(null).trigger('change');
                    citySelect.val(null).trigger('change');
                    
                    if (countryId) {
                        $.ajax({
                            url: '{{ route('admin.products.get-regions-by-country') }}',
                            type: 'GET',
                            data: { country_id: countryId },
                            success: function(data) {
                                regionSelect.empty().append('<option value="">{{__('trans.choose')}} {{__('trans.region.index')}}</option>');
                                $.each(data, function(key, value) {
                                    var selected = (value.id == selectedRegionId && countryId == selectedCountryId) ? 'selected' : '';
                                    regionSelect.append('<option value="' + value.id + '" ' + selected + '>' + value.name + '</option>');
                                });
                                regionSelect.trigger('change');
                                
                                // If region was pre-selected, load cities
                                if (selectedRegionId && countryId == selectedCountryId) {
                                    setTimeout(function() {
                                        loadCities(selectedRegionId);
                                    }, 100);
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('Error loading regions:', error);
                            }
                        });
                    }
                });
                
                // Handle region change
                $(document).on('change', '#select2Primary_region_id', function() {
                    var regionId = $(this).val();
                    loadCities(regionId);
                });
                
                function loadCities(regionId) {
                    var citySelect = $('#select2Primary_city_id');
                    
                    // Clear city dropdown
                    citySelect.empty().append('<option value="">{{__('trans.choose')}} {{__('trans.city.index')}}</option>');
                    citySelect.val(null).trigger('change');
                    
                    if (regionId) {
                        $.ajax({
                            url: '{{ route('admin.products.get-cities-by-region') }}',
                            type: 'GET',
                            data: { region_id: regionId },
                            success: function(data) {
                                citySelect.empty().append('<option value="">{{__('trans.choose')}} {{__('trans.city.index')}}</option>');
                                $.each(data, function(key, value) {
                                    var selected = (value.id == selectedCityId && regionId == selectedRegionId) ? 'selected' : '';
                                    citySelect.append('<option value="' + value.id + '" ' + selected + '>' + value.name + '</option>');
                                });
                                citySelect.trigger('change');
                            },
                            error: function(xhr, status, error) {
                                console.error('Error loading cities:', error);
                            }
                        });
                    }
                }
                
                // Initialize on page load if country is selected
                if (selectedCountryId) {
                    setTimeout(function() {
                        $('#select2Primary_country_id').trigger('change');
                    }, 200);
                }
            }, 500);
        });
    </script>
@endpush
