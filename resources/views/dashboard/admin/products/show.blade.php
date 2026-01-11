@extends('dashboard.admin.layout.main')

@section('title')
    {{$title = __('trans.product.show')}}
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

            <li class="breadcrumb-item active"> <i class="ti ti-file-database"></i> {{$title}}</li>
        </ol>
    </nav>

    <div class="card mb-4 mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">@lang('trans.product.details')</h5>
            <a href="{{ route('admin.products.child-products.index', $row) }}" class="btn btn-primary">
                <i class="ti ti-package me-1"></i> @lang('trans.child-product.manage')
            </a>
        </div>
        <div class="card-body">
            <div class="row g-3">
                {{-- Product Name --}}
                <div class="col-xl-6">
                    <label class="form-label fw-bold">@lang('trans.name')</label>
                    <p class="form-control-plaintext">{{ $row->name }}</p>
                </div>

                {{-- Description --}}
                <div class="col-xl-6">
                    <label class="form-label fw-bold">@lang('trans.description')</label>
                    <p class="form-control-plaintext">{{ $row->description ?? '-' }}</p>
                </div>

                {{-- Store --}}
                <div class="col-xl-6">
                    <label class="form-label fw-bold">@lang('trans.store.index')</label>
                    <p class="form-control-plaintext">{{ $row->store->name ?? '-' }}</p>
                </div>

                {{-- City --}}
                <div class="col-xl-6">
                    <label class="form-label fw-bold">@lang('trans.city.index')</label>
                    <p class="form-control-plaintext">{{ $row->city->name ?? '-' }}</p>
                </div>

                {{-- Category --}}
                <div class="col-xl-6">
                    <label class="form-label fw-bold">@lang('trans.category.index')</label>
                    <p class="form-control-plaintext">{{ $row->category->name ?? '-' }}</p>
                </div>

                {{-- Brand --}}
                <div class="col-xl-6">
                    <label class="form-label fw-bold">@lang('trans.brand.index')</label>
                    <p class="form-control-plaintext">{{ $row->brand->name ?? '-' }}</p>
                </div>

                {{-- Status --}}
                <div class="col-xl-6">
                    <label class="form-label fw-bold">@lang('trans.status')</label>
                    <p class="form-control-plaintext">
                        @if($row->is_active)
                            <span class="badge bg-success">@lang('trans.active')</span>
                        @else
                            <span class="badge bg-danger">@lang('trans.inactive')</span>
                        @endif
                    </p>
                </div>

                {{-- Image --}}
                @if($row->getFirstMedia('image'))
                <div class="col-xl-6">
                    <label class="form-label fw-bold">@lang('trans.image')</label>
                    <div>
                        <img src="{{ $row->getFirstMediaUrl('image', 'thumb') }}" alt="{{ $row->name }}" class="img-thumbnail" style="max-height: 150px;">
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Child Products Section --}}
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="ti ti-package me-2"></i>@lang('trans.child-product.index') ({{ $row->childProducts->count() }})</h5>
            <a href="{{ route('admin.products.child-products.create', $row) }}" class="btn btn-sm btn-success">
                <i class="ti ti-plus me-1"></i> @lang('trans.add')
            </a>
        </div>
        <div class="card-body">
            @if($row->childProducts->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>@lang('trans.price')</th>
                                <th>@lang('trans.quantity')</th>
                                <th>@lang('trans.expiry_date')</th>
                                <th>@lang('trans.production_line_number')</th>
                                <th>@lang('trans.status')</th>
                                <th>@lang('trans.actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($row->childProducts as $index => $child)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ number_format($child->price, 2) }}</td>
                                    <td>{{ $child->quantity }}</td>
                                    <td>{{ $child->expiry_date ? \Carbon\Carbon::parse($child->expiry_date)->format('Y-m-d') : '-' }}</td>
                                    <td>{{ $child->production_line_number ?? '-' }}</td>
                                    <td>
                                        @if($child->is_active)
                                            <span class="badge bg-success">@lang('trans.active')</span>
                                        @else
                                            <span class="badge bg-danger">@lang('trans.inactive')</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.products.child-products.edit', ['product' => $row->id, 'childProduct' => $child->id]) }}" class="btn btn-sm btn-icon">
                                            <i class="text-primary ti ti-edit"></i>
                                        </a>
                                        <a href="{{ route('admin.products.child-products.show', ['product' => $row->id, 'childProduct' => $child->id]) }}" class="btn btn-sm btn-icon">
                                            <i class="text-info ti ti-eye-check"></i>
                                        </a>
                                        <a class="btn btn-sm btn-icon delete-child-product" 
                                           data-url="{{ route('admin.products.child-products.destroy', ['product' => $row->id, 'childProduct' => $child->id]) }}">
                                            <i class="text-danger ti ti-trash-x"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <img src="{{asset('/storage/images/no_data.png')}}" width="150px" alt="">
                    <p class="mt-2 text-muted">@lang('trans.no_child_products')</p>
                </div>
            @endif
        </div>
    </div>

    <div class="pt-2 d-flex justify-content-center">
        <a class="btn btn-label-dribbble waves-effect" href="{{ route('admin.products.index') }}">@lang('trans.back')</a>
    </div>

@endsection

@push('js_files')
    <script>
        $(document).on('click', '.delete-child-product', function(e) {
            e.preventDefault();
            var url = $(this).data('url');
            var row = $(this).closest('tr');
            
            Swal.fire({
                title: '{{ __("trans.are_you_sure") }}',
                text: "{{ __('trans.you_wont_be_able_to_revert_this') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '{{ __("trans.yes_delete_it") }}',
                cancelButtonText: '{{ __("trans.cancel") }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            row.fadeOut(300, function() {
                                $(this).remove();
                            });
                            Swal.fire(
                                '{{ __("trans.deleted") }}',
                                '{{ __("trans.item_has_been_deleted") }}',
                                'success'
                            );
                        },
                        error: function(xhr) {
                            Swal.fire(
                                '{{ __("trans.error") }}',
                                xhr.responseJSON?.error || '{{ __("trans.something_went_wrong") }}',
                                'error'
                            );
                        }
                    });
                }
            });
        });
    </script>
@endpush
