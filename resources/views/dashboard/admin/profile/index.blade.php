@extends('dashboard.admin.layout.main')

@section('title', trans('trans.admin.my_profile'))

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <h5 class="card-header">{{ trans('trans.admin.my_profile') }}</h5>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-4">
                                <div class="d-flex align-items-start align-items-sm-center gap-4">
                                    <img src="{{ $admin->getFirstMediaUrl('profile') ?: asset('assets/img/avatars/1.png') }}"
                                         alt="user-avatar"
                                         class="d-block rounded"
                                         height="100"
                                         width="100"
                                         id="uploadedAvatar" />
                                    <div class="flex-grow-1">
                                        <h4 class="mb-1">{{ $admin->name }}</h4>
                                        <span class="badge bg-label-secondary">{{ $admin->email }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <ul class="nav nav-tabs" id="profileTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="true">
                                            <i class="ti ti-user me-1"></i>
                                            {{ trans('trans.admin.profile_information') }}
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button" role="tab" aria-controls="password" aria-selected="false">
                                            <i class="ti ti-lock me-1"></i>
                                            {{ trans('trans.admin.change_password') }}
                                        </button>
                                    </li>
                                </ul>
                                <div class="tab-content" id="profileTabsContent">
                                    <!-- Profile Information Tab -->
                                    <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                        <form id="submittedForm2" class="mt-3" action="{{ route('admin.profile.update') }}" method="POST">
                                            @csrf
                                            <div class="row">
                                                <x-admin.input required="true" :value="$admin->name" name="name" label="name" type="text" col="col-md-6" placeholder="name"/>
                                                <x-admin.input required="true" :value="$admin->email" name="email" label="email" type="email" col="col-md-6" placeholder="email"/>
                                                <div class="mb-3 col-md-12">
                                                    <x-admin.file :files="$admin->getMedia('profile')" name="profile" class="col-12" :multiple="false"/>
                                                </div>
                                            </div>
                                            <div class="mt-4">
                                                <button type="submit" class="btn btn-primary me-2 submit-button2" id="profileSubmitBtn">{{ trans('trans.admin.save_changes') }}</button>
                                                <button type="reset" class="btn btn-outline-secondary">{{ trans('trans.admin.cancel') }}</button>
                                            </div>
                                        </form>
                                    </div>

                                    <!-- Change Password Tab -->
                                    <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
                                        <form id="submittedForm" action="{{ route('admin.profile.change-password') }}" class="mt-3" method="POST" >
                                            @csrf
                                            <div class="row">
                                                <x-admin.input required="true" name="old_password" label="auth.old_password" type="password" col="col-md-12" placeholder="auth.old_password"/>
                                                <x-admin.input required="true" name="new_password" label="auth.new_password" type="password" col="col-md-6" placeholder="auth.new_password"/>
                                                <x-admin.input required="true" name="new_password_confirmation" label="auth.new_password_confirmation" type="password" col="col-md-6" placeholder="auth.new_password_confirmation"/>
                                            </div>
                                            <div class="mt-4">
                                                <button type="submit" class="btn btn-primary me-2 submit-button">{{ trans('trans.admin.change_password') }}</button>
                                                <button type="reset" class="btn btn-outline-secondary ">{{ trans('trans.admin.cancel') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js_files')

    @include('dashboard.shared.submit'  , ['button' => 'submit-button' , 'form' => 'submittedForm'])
    @include('dashboard.shared.submit'  , ['button' => 'submit-button2' , 'form' => 'submittedForm2'])

@endpush
