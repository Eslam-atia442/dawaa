@extends('dashboard.admin.layout.main')

@section('title')
    {{$title = __('trans.user.show')}}
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
                <i class="ti ti-user me-2"></i>
                <a href="{{route('admin.users.index')}}">@lang('trans.user.index')</a>
            </li>

            <li class="breadcrumb-item active"> <i class="ti ti-file-database"></i> {{$title}}</li>
        </ol>
    </nav>

    <div class="card mb-4 mt-4">
        <div class="card-body" >

            <div class="row g-3">

{{--                @foreach (languages() as $lang)
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
                --}}
            </div>
        </div>
    </div>
    </div>

@endsection

@push('js_files')
    @include('dashboard.shared.submitAddForm')
    @include('dashboard.shared.addImage')
@endpush
