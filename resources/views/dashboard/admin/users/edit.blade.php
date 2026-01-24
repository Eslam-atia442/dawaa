@extends('dashboard.admin.layout.main')

@section('title')
    {{$title = __('trans.user.edit')}}
@endsection

@push('css_files')
    <link rel="stylesheet" href="{{asset('assets/validation/form-validation.css')}}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #map {
            height: 400px;
            width: 100%;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
    </style>
@endpush

@section('content')

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <i class="ti ti-home-bolt me-2"></i>
                 <a href="{{route('admin.home')}}">@lang('trans.home')</a>
            </li>

            <li class="breadcrumb-item">
                <i class="ti ti-user me-2"></i>
                <a href="{{route('admin.users.index')}}">@lang('trans.user.index')</a>
            </li>

            <li class="breadcrumb-item active"> <i class="ti ti-file-pencil me-2"></i> {{$title}}</li>
        </ol>
    </nav>

    <div class="card mb-4 mt-4">
        <form class="card-body form validated-form" method="POST" action="{{route('admin.users.update' , ['user' => $row->id])}}" novalidate enctype="multipart/form-data">
            @csrf
            
            <input type="hidden" name="lat" id="lat" value="{{$row->lat}}">
            <input type="hidden" name="long" id="long" value="{{$row->long}}">

            <div class="row g-3">
                <x-admin.input 
                    :value="$row->type?->value" 
                    required="true" 
                    name="type" 
                    label="type" 
                    type="select" 
                    col="col-xl-6" 
                    placeholder="type" 
                    :options="$userTypes"
                />
                
                <x-admin.input 
                    :value="$row->name" 
                    required="true" 
                    name="name" 
                    label="name" 
                    type="text" 
                    col="col-xl-6" 
                    placeholder="name"
                />
                
                <x-admin.input 
                    :value="$row->phone" 
                    name="phone" 
                    label="phone"
                    type="text" 
                    col="col-xl-6" 
                    placeholder="phone"
                />
                <x-admin.input 
                    :value="$row->email" 
                    name="email" 
                    label="email"
                    type="email" 
                    col="col-xl-6" 
                    placeholder="email"
                />
                <x-admin.input 
                    name="password" 
                    label="password"
                    type="password" 
                    col="col-xl-6" 
                    placeholder="password"
                />
                <x-admin.input 
                    name="password_confirmation" 
                    label="password_confirmation"
                    type="password" 
                    col="col-xl-6" 
                    placeholder="password_confirmation"
                />
                <div class="row g-3">   
                    <x-admin.file
                        name="license"
                        label="license"
                        type="file"
                        col="col-6"
                        placeholder="license"
                        :files="$row->getMedia('license')"
                    />
                    <x-admin.file
                        name="tax_card"
                        label="tax_card"
                        type="file"
                        col="col-6"
                        placeholder="tax_card"
                        :files="$row->getMedia('tax_card')"
                    />
                    <x-admin.file
                        name="front_card_image"
                        label="front_card_image"
                        type="file"
                        col="col-6"
                        placeholder="front_card_image"
                        :files="$row->getMedia('front_card_image')"
                    />
                    <x-admin.file
                        name="back_card_image"
                        label="back_card_image"
                        type="file"
                        col="col-6"
                        placeholder="back_card_image"
                        :files="$row->getMedia('back_card_image')"
                    />
                </div>

                <x-admin.input
                    :value="$row->map_description"
                    name="map_description"
                    label="map_description"
                    type="text"
                    col="col-12"
                    placeholder="map_description"
                />

                <x-admin.textarea
                    :value="$row->note"
                    name="note"
                    label="note"
                    col="col-12"
                    placeholder="note"
                />
            </div>

            <div class="row g-3 mt-4">
                <div class="col-12">
                    <h5 class="mb-3">{{ __('trans.location') }}</h5>
                    <div id="map"></div>
                </div>
            </div>

            <div class="pt-4 d-flex justify-content-center mt-3">
                <button type="submit"
                        class="btn btn-primary me-sm-3 me-1 waves-effect waves-light submit-button">@lang('trans.edit')</button>
                <a class="btn btn-label-dribbble waves-effect" href="{{ url()->previous()}}">@lang('trans.back')</a>
            </div>
        </form>
    </div>

@endsection

@push('js_files')
    @include('dashboard.shared.submitEditForm')
    @include('dashboard.shared.addImage')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof L === 'undefined') {
                console.error('Leaflet is not loaded');
                return;
            }

            var lat = parseFloat(@json($row->lat));
            var lng = parseFloat(@json($row->long));
            var mapDesc = @json($row->map_description ?? '');

            // Default fallback if no location (e.g., Cairo)
            if (isNaN(lat) || isNaN(lng)) {
                lat = 30.0444; 
                lng = 31.2357; 
            }

            var map = L.map('map').setView([lat, lng], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            var marker;

            // If we had valid initial lat/lng, show marker
            if (@json($row->lat) && @json($row->long)) {
                marker = L.marker([lat, lng], {draggable: true}).addTo(map);
                marker.bindPopup(mapDesc).openPopup();
                
                // Update inputs on drag end
                marker.on('dragend', function(e) {
                    var position = marker.getLatLng();
                    updateInputs(position.lat, position.lng);
                });
            }

            // Map click event
            map.on('click', function(e) {
                if (marker) {
                    marker.setLatLng(e.latlng);
                } else {
                    marker = L.marker(e.latlng, {draggable: true}).addTo(map);
                    marker.on('dragend', function(e) {
                        var position = marker.getLatLng();
                        updateInputs(position.lat, position.lng);
                    });
                }
                updateInputs(e.latlng.lat, e.latlng.lng);
            });

            function updateInputs(lat, lng) {
                document.getElementById('lat').value = lat;
                document.getElementById('long').value = lng;
            }

             // Force resize to fix gray tiles
             setTimeout(function() {
                map.invalidateSize();
            }, 500);
        });
    </script>
@endpush
