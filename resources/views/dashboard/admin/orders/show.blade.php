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

                {{-- Updated At --}}
                <div class="col-xl-6">
                    <label class="form-label fw-bold">@lang('trans.updated_at')</label>
                    <p class="form-control-plaintext">{{ $row->updated_at?->format('Y-m-d H:i:s') }}</p>
                </div>
            </div>
        </div>
    </div>

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

    <div class="pt-2 d-flex justify-content-center">
        <a class="btn btn-label-dribbble waves-effect" href="{{ route('admin.orders.index') }}">@lang('trans.back')</a>
    </div>

@endsection

@push('js_files')
@endpush
