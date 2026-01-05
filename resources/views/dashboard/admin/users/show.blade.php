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
    <div class="card-body">
        <div class="row g-3">
            <x-admin.input
                :value="$row->id"
                name="id"
                label="id"
                type="text"
                col="col-xl-6"
                placeholder="id"
                disabled="true" />

            <x-admin.input
                :value="$row->type?->label()"
                name="type"
                label="type"
                type="text"
                col="col-xl-6"
                placeholder="type"
                disabled="true" />

            <x-admin.input
                :value="$row->name"
                name="name"
                label="name"
                type="text"
                col="col-xl-6"
                placeholder="name"
                disabled="true" />

            <x-admin.input
                :value="$row->phone"
                name="phone"
                label="phone"
                type="text"
                col="col-xl-6"
                placeholder="phone"
                disabled="true" />

            <x-admin.input
                :value="$row->email"
                name="email"
                label="email"
                type="email"
                col="col-xl-6"
                placeholder="email"
                disabled="true" />

            <x-admin.input
                :value="$row->code ?? '-'"
                name="code"
                label="otp"
                type="text"
                col="col-xl-6"
                placeholder="code"
                disabled="true" />

            <x-admin.input
                :value="$row->code_expires_at ? $row->code_expires_at->format('Y-m-d H:i:s') : '-'"
                name="code_expires_at"
                label="code_expires_at"
                type="text"
                col="col-xl-6"
                placeholder="code_expires_at"
                disabled="true" />

            <x-admin.input
                :value="$row->email_verified_at ? $row->email_verified_at->format('Y-m-d H:i:s') : __('trans.not_verified')"
                name="email_verified_at"
                label="email_verified"
                type="text"
                col="col-xl-6"
                placeholder="email_verified_at"
                disabled="true" />


            @if($row->country)
            <x-admin.input
                :value="$row->country->name ?? '-'"
                name="country_id"
                label="country.index"
                type="text"
                col="col-xl-6"
                placeholder="country.index"
                disabled="true" />
            @endif

            @if($row->dob)
            <x-admin.input
                :value="$row->dob ? \Carbon\Carbon::parse($row->dob)->format('Y-m-d') : '-'"
                name="dob"
                label="dob"
                type="text"
                col="col-xl-6"
                placeholder="dob"
                disabled="true" />
            @endif

            @if($row->social_type)
            <x-admin.input
                :value="ucfirst($row->social_type)"
                name="social_type"
                label="social_type"
                type="text"
                col="col-xl-6"
                placeholder="social_type"
                disabled="true" />
            @endif

            <x-admin.input
                :value="$row->is_active ? __('trans.active') : __('trans.inactive')"
                name="is_active"
                label="active"
                type="text"
                col="col-xl-6"
                placeholder="is_active"
                disabled="true" />

            <x-admin.input
                :value="$row->is_blocked ? __('trans.block') : __('trans.unblock')"
                name="is_blocked"
                label="block_status"
                type="text"
                col="col-xl-6"
                placeholder="is_blocked"
                disabled="true" />

            <x-admin.input
                :value="$row->is_accepted ? __('trans.accepted') : __('trans.not_accepted')"
                name="is_accepted"
                label="acceptance_status"
                type="text"
                col="col-xl-6"
                placeholder="is_accepted"
                disabled="true" />

            <x-admin.input
                :value="$row->created_at ? $row->created_at->format('Y-m-d H:i:s') : '-'"
                name="created_at"
                label="created_at"
                type="text"
                col="col-xl-6"
                placeholder="created_at"
                disabled="true" />

            <x-admin.input
                :value="$row->updated_at ? $row->updated_at->format('Y-m-d H:i:s') : '-'"
                name="updated_at"
                label="updated_at"
                type="text"
                col="col-xl-6"
                placeholder="updated_at"
                disabled="true" />
        </div>

        <div class="row g-3 mt-3">
            <div class="col-12">
                <h5 class="mb-3">{{ __('trans.attachments') }}</h5>
            </div>

            <x-admin.file
                :files="$row->getMedia('license')"
                name="license"
                label="license"
                class="col-6"
                :multiple="false"
                accept="*/*"
                disabled="true" />

            <x-admin.file
                :files="$row->getMedia('tax_card')"
                name="tax_card"
                label="tax_card"
                class="col-6"
                :multiple="false"
                accept="*/*"
                disabled="true" />

            <x-admin.file
                :files="$row->getMedia('front_card_image')"
                name="front_card_image"
                label="front_card_image"
                class="col-6"
                :multiple="false"
                accept="image/*"
                disabled="true" />

            <x-admin.file
                :files="$row->getMedia('back_card_image')"
                name="back_card_image"
                label="back_card_image"
                class="col-6"
                :multiple="false"
                accept="image/*"
                disabled="true" />
        </div>

        @can('accept-user-account-user')
            @if(!$row->is_accepted)
            <div class="row g-3 mt-4">
                <div class="col-12">
                    <button type="button" class="btn btn-success accept-account-btn" data-url="{{ route('admin.users.accept', $row->id) }}">
                        <i class="ti ti-check me-2"></i>
                        {{ __('trans.accept_account') }}
                    </button>
                </div>
            </div>
            @else
            <div class="row g-3 mt-4">
                <div class="col-12">
                    <div class="alert alert-success" role="alert">
                        <i class="ti ti-check-circle me-2"></i>
                        {{ __('trans.account_already_accepted') }}
                    </div>
                </div>
            </div>
            @endif
        @endcan
    </div>
</div>

@endsection

@push('js_files')
@include('dashboard.shared.submitAddForm')
@include('dashboard.shared.addImage')
@can('accept-user-account-user')
<script>
    $(document).on('click', '.accept-account-btn', function(e) {
        e.preventDefault();

        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        var url = $(this).data('url');
        var button = $(this);

        Swal.fire({
            title: `{{ __('trans.are_you_sure') }}`,
            text: `{{ __('trans.accept_account_confirmation') }}`,
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#28a745",
            cancelButtonColor: "#6c757d",
            confirmButtonText: `{{ __('trans.yes_accept_it') }}`,
            cancelButtonText: `{{ __('trans.no_cancel_it') }}`,
        }).then((result) => {
            if (result.isConfirmed) {
                button.prop('disabled', true);
                button.html('<i class="ti ti-loader me-2"></i>{{ __('
                    trans.processing ') }}...');

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    type: "POST",
                    url: url,
                    success: (response) => {
                        Swal.fire({
                            icon: 'success',
                            position: 'top-center',
                            title: response.message || '{{ __('
                            trans.account_accepted_successfully ') }}',
                            showConfirmButton: false,
                            timer: 2000
                        });
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    },
                    error: (jqXHR, textStatus, errorThrown) => {
                        button.prop('disabled', false);
                        button.html('<i class="ti ti-check me-2"></i>{{ __('
                            trans.accept_account ') }}');
                        Swal.fire({
                            icon: 'error',
                            position: 'top-center',
                            title: jqXHR.responseJSON?.message || '{{ __('
                            trans.error_occurred_during_operation ') }}',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    }
                });
            }
        });
    });
</script>
@endcan
@endpush