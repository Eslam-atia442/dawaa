@extends('dashboard.admin.layout.main')

@section('title')
{{$title = __('trans.quantity_history')}}
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
            <a href="{{route('admin.products.index')}}">@lang('trans.product.index')</a>
        </li>

        <li class="breadcrumb-item">
            <i class="ti ti-package me-2"></i>
            <a href="{{route('admin.products.show', $product)}}">{{ $product->name }}</a>
        </li>

        <li class="breadcrumb-item">
            <i class="ti ti-packages me-2"></i>
            <a href="{{route('admin.products.child-products.index', $product)}}">@lang('trans.child-product.index')</a>
        </li>

        <li class="breadcrumb-item">
            <i class="ti ti-package me-2"></i>
            <a href="{{route('admin.products.child-products.show', [$product, $childProduct])}}">{{ $childProduct->name }}</a>
        </li>

        <li class="breadcrumb-item active"> <i class="ti ti-history"></i> {{$title}}</li>
    </ol>
</nav>

<div class="card mb-4 mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">{{ $title }} - {{ $childProduct->name }}</h5>
        <a href="{{route('admin.products.child-products.show', [$product, $childProduct])}}" class="btn btn-outline-primary">
            <i class="ti ti-arrow-left me-1"></i>
            @lang('trans.back')
        </a>
    </div>
    <div class="card-body">
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h4 class="card-title">{{ $childProduct->quantity }}</h4>
                        <p class="card-text mb-0">@lang('trans.quantity')</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-{{ $childProduct->is_active ? 'success' : 'warning' }} text-white">
                    <div class="card-body text-center">
                        <h4 class="card-title">
                            @if($childProduct->is_active)
                                <i class="ti ti-check-circle"></i>
                            @else
                                <i class="ti ti-x-circle"></i>
                            @endif
                        </h4>
                        <p class="card-text mb-0">
                            @lang($childProduct->is_active ? 'trans.active' : 'trans.inactive')
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h4 class="card-title">{{ $quantityHistory->total() }}</h4>
                        <p class="card-text mb-0">@lang('trans.total_records')</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>@lang('trans.id')</th>
                        <th>@lang('trans.type')</th>
                        <th>@lang('trans.quantity_change')</th>
                        <th>@lang('trans.quantity_before')</th>
                        <th>@lang('trans.quantity_after')</th>
                        <th>@lang('trans.reason')</th>
                        <th>@lang('trans.reference')</th>
                        <th>@lang('trans.note')</th>
                        <th>@lang('trans.admins')</th>
                        <th>@lang('trans.created_at')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quantityHistory as $history)
                        <tr>
                            <td>{{ $history->id }}</td>
                            <td>
                                <span class="badge bg-{{ $history->type === 'credit' ? 'success' : 'danger' }}">
                                    {{ $history->type_label }}
                                </span>
                            </td>
                            <td class="{{ $history->type === 'credit' ? 'text-success' : 'text-danger' }}">
                                {{ $history->type === 'credit' ? '+' : '' }}{{ $history->quantity_change }}
                            </td>
                            <td>{{ $history->quantity_before }}</td>
                            <td>{{ $history->quantity_after }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ $history->reason_label }}</span>
                            </td>
                            <td>
                                @if($history->reference_type && $history->reference_id)
                                    {{ ucfirst($history->reference_type) }} #{{ $history->reference_id }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $history->notes ?? '-' }}</td>
                            <td>{{ $history->admin?->name ?? '-' }}</td>
                            <td>{{ $history->created_at?->format('Y-m-d H:i:s') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-4">
                                <div class="d-flex flex-column align-items-center">
                                    <img src="{{globalSetting('no_data_image')?->first()?->getFullUrl()}} " width="100px" alt="">
                                    <span class="mt-2">{{ __('trans.no_data_found') }}</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($quantityHistory->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $quantityHistory->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </div>
</div>

@endsection

@push('js_files')
<script>
    $(document).ready(function() {
        // Any additional JavaScript can be added here
    });
</script>
@endpush