@extends('dashboard.admin.layout.main')

@section('title')
    {{$title = __('Export Details')}}
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
                <i class="ti ti-file-export me-2"></i>
                <a href="{{route('admin.exports.index')}}">{{ __('trans.export_management') }}</a>
            </li>

            <li class="breadcrumb-item active"> <i class="ti ti-file-database"></i> {{$title}}</li>
        </ol>
    </nav>

    <div class="card mb-4 mt-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-muted">{{ __('Export Information') }}</h6>
                    <table class="table table-borderless">
                        <tr>
                            <td class="fw-bold">{{ __('Name') }}:</td>
                            <td>{{ $row->name }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">{{ __('Model') }}:</td>
                            <td>{{ $row->model }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">{{ __('Status') }}:</td>
                            <td>
                                <span class="badge bg-{{ $row->status_color }}">{{ $row->status_label }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">{{ __('Total Records') }}:</td>
                            <td>{{ $row->total_records ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">{{ __('Created At') }}:</td>
                            <td>{{ $row->created_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">{{ __('Completed At') }}:</td>
                            <td>{{ $row->completed_at ? $row->completed_at->format('Y-m-d H:i:s') : '-' }}</td>
                        </tr>
                        @if($row->error_message)
                        <tr>
                            <td class="fw-bold">{{ __('Error Message') }}:</td>
                            <td class="text-danger">{{ $row->error_message }}</td>
                        </tr>
                        @endif
                    </table>
                </div>

                <div class="col-md-6">
                    <h6 class="text-muted">{{ __('User Information') }}</h6>
                    <table class="table table-borderless">
                        <tr>
                            <td class="fw-bold">{{ __('Exported By') }}:</td>
                            <td>{{ $row->user->name }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">{{ __('Email') }}:</td>
                            <td>{{ $row->user->email }}</td>
                        </tr>
                    </table>

                    @if($row->parameters)
                    <h6 class="text-muted mt-4">{{ __('Export Parameters') }}</h6>
                    <div class="bg-light p-3 rounded">
                        <pre class="mb-0">{{ json_encode($row->parameters, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                    @endif
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.exports.index') }}" class="btn btn-secondary">
                            <i class="ti ti-arrow-left me-1"></i>
                            {{ __('Back to Exports') }}
                        </a>

                        @if($row->isReady())
                        <a href="{{ route('admin.exports.download', $row) }}" class="btn btn-success">
                            <i class="ti ti-download me-1"></i>
                            {{ __('Download File') }}
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
