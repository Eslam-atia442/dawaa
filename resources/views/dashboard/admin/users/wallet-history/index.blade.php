@extends('dashboard.admin.layout.main')

@section('title')
{{$title = __('trans.user_wallet_history')}}
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

        <li class="breadcrumb-item">
            <i class="ti ti-user me-2"></i>
            <a href="{{route('admin.users.show', $user)}}">{{ $user->name }}</a>
        </li>

        <li class="breadcrumb-item active"> <i class="ti ti-history"></i> {{$title}}</li>
    </ol>
</nav>

<div class="card">
    <div class="card-header">
        <x-admin.buttons
            extrabuttons="true"
            :addbutton="false"
            :deletebutton="false"
        >
            <x-slot name="extrabuttonsdiv">
                @can('update-user')
                <button type="button" class="btn btn-success waves-effect extrabuttonsdiv me-2" data-bs-toggle="modal" data-bs-target="#addBalanceModal">
                    <i class="ti ti-plus me-1"></i>
                    @lang('trans.add_balance')
                </button>
                @endcan

                @can('create-export')
                    <x-admin.export-button 
                        :route="route('admin.users.wallet-history-export', $user)"
                        buttonId="exportWalletHistoryBtn"
                        buttonClass="btn btn-outline-success waves-effect extrabuttonsdiv me-2"
                    />  
                @endcan

                <a href="{{route('admin.users.show', $user)}}" class="btn btn-outline-primary waves-effect extrabuttonsdiv">
                    <i class="ti ti-arrow-left me-1"></i>
                    @lang('trans.back')
                </a>
            </x-slot>
        </x-admin.buttons>

        <x-admin.filter
            datefilter="true"
            order="true"
            :searchArray="[
                'type' => [
                    'input_type' => 'select',
                    'input_name' => __('trans.type'),
                    'rows' => [
                        ['id' => '', 'name' => __('trans.all')],
                        ['id' => \App\Enums\WalletTransactionTypeEnum::add->value, 'name' => __('trans.credit')],
                        ['id' => \App\Enums\WalletTransactionTypeEnum::deduct->value, 'name' => __('trans.debit')],
                    ]
                ],
                'reference_type' => [
                    'input_type' => 'text',
                    'input_name' => __('trans.reference_type'),
                ],
            ]"
        />
    </div>

    <div class="card-body">
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h4 class="card-title">{{ $user->wallet?->balance ?? 0 }}</h4>
                        <p class="card-text mb-0">@lang('trans.wallet_balance')</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-{{ $user->wallet?->status == 1 ? 'success' : 'warning' }} text-white">
                    <div class="card-body text-center">
                        <h4 class="card-title">
                            @if($user->wallet?->status == 1)
                                <i class="ti ti-check-circle"></i>
                            @else
                                <i class="ti ti-x-circle"></i>
                            @endif
                        </h4>
                        <p class="card-text mb-0">
                            @lang($user->wallet?->status == 1 ? 'trans.active' : 'trans.inactive')
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-datatable table-responsive table_content_append">
    </div>
</div>

<!-- Add Balance Modal -->
<div class="modal fade" id="addBalanceModal" tabindex="-1" aria-labelledby="addBalanceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addBalanceModalLabel">@lang('trans.add_balance')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addBalanceForm" action="{{ route('admin.users.add-balance', $user) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="amount" class="form-label">@lang('trans.amount')</label>
                        <input type="number" step="0.01" min="0.01" class="form-control" id="amount" name="amount" required>
                        <div class="invalid-feedback">@lang('trans.this_field_is_required')</div>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">@lang('trans.notes')</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="@lang('trans.notes')"></textarea>
                    </div>
                    <div class="alert alert-info">
                        <i class="ti ti-info-circle me-2"></i>
                        @lang('trans.add_balance_confirmation')
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('trans.cancel')</button>
                    <button type="submit" class="btn btn-success">
                        <i class="ti ti-plus me-1"></i>
                        @lang('trans.add_balance')
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('js_files')
<script>
    $(document).ready(function() {
        $('#addBalanceForm').on('submit', function(e) {
            e.preventDefault();

            const form = $(this);
            const submitBtn = form.find('button[type="submit"]');
            const originalText = submitBtn.html();

            submitBtn.prop('disabled', true);
            submitBtn.html('<i class="ti ti-loader me-1"></i>Processing...');

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    $('#addBalanceModal').modal('hide');
                    form[0].reset();

                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message || 'Balance updated successfully',
                        timer: 2000,
                        showConfirmButton: false
                    });

                    setTimeout(function() {
                        window.location.reload();
                    }, 1500);
                },
                error: function(xhr) {
                    let errorMessage = 'Something went wrong';

                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMessage
                    });
                },
                complete: function() {
                    submitBtn.prop('disabled', false);
                    submitBtn.html(originalText);
                }
            });
        });

        $('#addBalanceModal').on('hidden.bs.modal', function() {
            $('#addBalanceForm')[0].reset();
            $('#addBalanceForm .is-invalid').removeClass('is-invalid');
        });
    });
</script>

@include('dashboard.shared.deleteAll')
@include('dashboard.shared.deleteOne')
@include('dashboard.shared.filter_js', ['index_route' => route('admin.users.wallet-history', $user)])
@endpush
