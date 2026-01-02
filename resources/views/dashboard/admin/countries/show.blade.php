@extends('dashboard.admin.layout.main')

@section('title')
    {{$title = __('trans.country.show')}}
@endsection

@section('content')

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <i class="ti ti-home-bolt me-2"></i>
                 <a href="{{route('admin.home')}}">@lang('trans.home')</a>
            </li>

            <li class="breadcrumb-item">
                <i class="ti ti-building-skyscraper me-2"></i>
                <a href="{{route('admin.countries.index')}}">@lang('trans.country.index')</a>
            </li>

            <li class="breadcrumb-item active"><i class="ti ti-eye me-2"></i> {{$title}}</li>
        </ol>
    </nav>

    <div class="card mb-4 mt-4">
        <div class="row g-0">
            <div class="col-md-3 border-end">
                <div class="nav flex-column nav-pills gap-2 py-4" id="v-pills-tab" role="tablist"
                     aria-orientation="vertical">
                    <button class="nav-link active d-flex align-items-center" id="v-pills-general-tab"
                            data-bs-toggle="pill" data-bs-target="#v-pills-general" type="button" role="tab"
                            aria-controls="v-pills-general" aria-selected="true">
                        <i class="ti ti-settings me-2"></i> @lang('trans.general')
                    </button>
                </div>
            </div>
            <div class="col-md-9">
                <div class="card-body p-4">
                    <div class="tab-content" id="v-pills-tabContent">
                        <div class="tab-pane fade show active" id="v-pills-general" role="tabpanel"
                             aria-labelledby="v-pills-general-tab" tabindex="0">
                            <div class="row g-3">
                                @foreach (languages() as $lang)
                                    <div class="col-xl-6">
                                        <label class="form-label">@lang('trans.name') ({{ strtoupper($lang) }})</label>
                                        <p class="form-control-plaintext">{{ $row->getTranslation('name', $lang) }}</p>
                                    </div>
                                @endforeach

                                @foreach (languages() as $lang)
                                    <div class="col-xl-6">
                                        <label class="form-label">@lang('trans.currency') ({{ strtoupper($lang) }})</label>
                                        <p class="form-control-plaintext">{{ $row->getTranslation('currency', $lang) }}</p>
                                    </div>
                                @endforeach

                                <div class="col-xl-6">
                                    <label class="form-label">@lang('trans.currency_code')</label>
                                    <p class="form-control-plaintext">{{ $row->currency_code }}</p>
                                </div>

                                <div class="col-xl-6">
                                    <label class="form-label">@lang('trans.country_code')</label>
                                    <p class="form-control-plaintext">{{ $row->key }}</p>
                                </div>

                                <div class="col-xl-6">
                                    <label class="form-label">@lang('trans.flag')</label>
                                    <p class="form-control-plaintext">{{ $row->iso2 }}</p>
                                </div>

                                <div class="col-xl-6">
                                    <label class="form-label">@lang('trans.is_active')</label>
                                    <p class="form-control-plaintext">
                                        @if($row->is_active)
                                            <span class="badge bg-success">@lang('trans.active')</span>
                                        @else
                                            <span class="badge bg-danger">@lang('trans.inactive')</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="pt-4 d-flex justify-content-center mt-3">
                        <a class="btn btn-label-dribbble waves-effect" href="{{ route('admin.countries.index') }}">@lang('trans.back')</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
