@extends('dashboard.admin.layout.main')

@section('title')
    {{$title = __('trans.order.show')}}
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
                <i class="ti ti-shopping-cart me-2"></i>
                <a href="{{route('admin.orders.index')}}">@lang('trans.order.index')</a>
            </li>

            <li class="breadcrumb-item active"> <i class="ti ti-file-database"></i> {{$title}}</li>
        </ol>
    </nav>

    <div class="card mb-4 mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">@lang('trans.order.details')</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                {{-- Order ID --}}
                <div class="col-xl-6">
                    <label class="form-label fw-bold">@lang('trans.id')</label>
                    <p class="form-control-plaintext">{{ $row->id }}</p>
                </div>

                {{-- User --}}
                <div class="col-xl-6">
                    <label class="form-label fw-bold">@lang('trans.user.index')</label>
                    <p class="form-control-plaintext">{{ $row->user->name ?? '-' }}</p>
                </div>

                {{-- Total Price --}}
                <div class="col-xl-6">
                    <label class="form-label fw-bold">@lang('trans.total_price')</label>
                    <p class="form-control-plaintext">{{ number_format($row->total_price, 2) }}</p>
                </div>

                {{-- Payment Type --}}
                <div class="col-xl-6">
                    <label class="form-label fw-bold">@lang('trans.payment_type')</label>
                    <p class="form-control-plaintext">{{ $row->payment_type?->label() }}</p>
                </div>

                {{-- Created At --}}
                <div class="col-xl-6">
                    <label class="form-label fw-bold">@lang('trans.created_at')</label>
                    <p class="form-control-plaintext">{{ $row->created_at?->format('Y-m-d H:i:s') }}</p>
                </div>

                {{-- Status --}}
                <div class="col-xl-6">
                    <label class="form-label fw-bold">@lang('trans.status')</label>
                    <p class="form-control-plaintext">
                        @if($row->status)
                            <span class="badge bg-{{ $row->status->value === 6 ? 'warning' : ($row->status->value === 7 ? 'success' : ($row->status->value === 8 ? 'danger' : 'secondary')) }}">
                                {{ $row->status->label() }}
                            </span>
                        @endif
                    </p>
                </div>

                {{-- Updated At --}}
                <div class="col-xl-6">
                    <label class="form-label fw-bold">@lang('trans.updated_at')</label>
                    <p class="form-control-plaintext">{{ $row->updated_at?->format('Y-m-d H:i:s') }}</p>
                </div>

                {{-- Note --}}
                @if($row->note)
                <div class="col-xl-12">
                    <label class="form-label fw-bold">@lang('trans.note')</label>
                    <p class="form-control-plaintext">{{ $row->note }}</p>
                </div>
                @endif

                {{-- Parent Order (Refund Reference) --}}
                @if($row->parent_id)
                <div class="col-xl-6">
                    <label class="form-label fw-bold">@lang('trans.refund_for_order')</label>
                    <p class="form-control-plaintext">
                        <a href="{{ route('admin.orders.show', $row->parentOrder) }}" class="text-primary">#{{ $row->parentOrder->id }}</a>
                    </p>
                </div>
                @endif

                {{-- Refund Type --}}
                @if($row->parent_id)
                <div class="col-xl-6">
                    <label class="form-label fw-bold">@lang('trans.refund_type')</label>
                    <p class="form-control-plaintext">{{ $row->refund_type?->label() }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Refund Actions (for pending refund requests) --}}
    @if($row->status && $row->status->value === 6) {{-- REFUND_REQUESTED --}}
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="ti ti-refresh-alert me-2"></i>@lang('trans.refund_request')</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-warning">
                <i class="ti ti-alert-circle me-2"></i>
                @lang('trans.refund_request_pending_approval')
            </div>
            <div class="d-flex gap-2">
                @can('approve-refund-order')
                <button type="button" class="btn btn-success" onclick="approveRefund({{ $row->id }})">
                    <i class="ti ti-check me-1"></i>@lang('trans.approve')
                </button>
                <button type="button" class="btn btn-danger" onclick="rejectRefund({{ $row->id }})">
                    <i class="ti ti-x me-1"></i>@lang('trans.reject')
                </button>
                @endcan
            </div>
        </div>
    </div>
    @endif

    {{-- Order Items Section --}}
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="ti ti-package me-2"></i>@lang('trans.order_items') ({{ $row->items->count() }})</h5>
        </div>
        <div class="card-body">
            @if($row->items->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>@lang('trans.product.index')</th>
                                <th>@lang('trans.quantity')</th>
                                <th>@lang('trans.price')</th>
                                <th>@lang('trans.total')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($row->items as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->product->name ?? '-' }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ number_format($item->price, 2) }}</td>
                                    <td>{{ number_format($item->quantity * $item->price, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <img src="{{asset('/storage/images/no_data.png')}}" width="150px" alt="">
                    <p class="mt-2 text-muted">@lang('trans.no_order_items')</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Refund Orders Section --}}
    @if($row->refundOrders && $row->refundOrders->count() > 0)
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="ti ti-refresh me-2"></i>@lang('trans.refund_orders') ({{ $row->refundOrders->count() }})</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>@lang('trans.id')</th>
                            <th>@lang('trans.total_price')</th>
                            <th>@lang('trans.refund_type')</th>
                            <th>@lang('trans.note')</th>
                            <th>@lang('trans.created_at')</th>
                            <th>@lang('trans.actions')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($row->refundOrders as $refund)
                        <tr>
                            <td>#{{ $refund->id }}</td>
                            <td>{{ number_format($refund->total_price, 2) }}</td>
                            <td>{{ $refund->refund_type?->label() }}</td>
                            <td>{{ $refund->note ?: '-' }}</td>
                            <td>{{ $refund->created_at?->format('Y-m-d H:i:s') }}</td>
                            <td>
                                <a href="{{ route('admin.orders.show', $refund) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="ti ti-eye"></i> @lang('trans.view')
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <div class="pt-2 d-flex justify-content-center">
        <a class="btn btn-label-dribbble waves-effect" href="{{ route('admin.orders.index') }}">@lang('trans.back')</a>
    </div>

@endsection

@push('js_files')
<script>
function approveRefund(orderId) {
    Swal.fire({
        title: '@lang('trans.confirm_approve_refund')',
        text: '@lang('trans.confirm_approve_refund_text', ['This action will process the refund and cannot be undone.'])',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '@lang('trans.approve')',
        cancelButtonText: '@lang('trans.cancel')'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("admin.orders.approve-refund", ":id") }}'.replace(':id', orderId),
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: '@lang('trans.success')',
                            text: response.message,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: '@lang('trans.error')',
                            text: response.message,
                            icon: 'error'
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        title: '@lang('trans.error')',
                        text: '@lang('trans.error_occurred')',
                        icon: 'error'
                    });
                }
            });
        }
    });
}

function rejectRefund(orderId) {
    Swal.fire({
        title: '@lang('trans.confirm_reject_refund')',
        text: '@lang('trans.confirm_reject_refund_text', ['This action will reject the refund request.'])',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '@lang('trans.reject')',
        cancelButtonText: '@lang('trans.cancel')'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("admin.orders.reject-refund", ":id") }}'.replace(':id', orderId),
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: '@lang('trans.success')',
                            text: response.message,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: '@lang('trans.error')',
                            text: response.message,
                            icon: 'error'
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        title: '@lang('trans.error')',
                        text: '@lang('trans.error_occurred')',
                        icon: 'error'
                    });
                }
            });
        }
    });
}
</script>
@endpush
