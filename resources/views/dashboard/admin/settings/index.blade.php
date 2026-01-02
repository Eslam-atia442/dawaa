@extends('dashboard.admin.layout.main')

@section('title')
    {{$title = __('trans.setting.index')}}
@endsection

@push('css_files')
    <!-- Remove stepper CSS -->
    <!-- Optionally add custom CSS for modern look -->
    <style>
        .nav-pills .nav-link {
            font-size: 1.1rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
        }
        .tab-content .tab-pane {
            padding-top: 2rem;
        }
        .card-header {
            background: linear-gradient(90deg, #f8fafc 0%, #e9ecef 100%);
            border-bottom: 1px solid #e3e6ed;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .card-header .ti {
            font-size: 1.5rem;
            color: #7367f0;
        }
        .modern-btn {
            font-size: 1.1rem;
            padding: 0.6rem 2rem;
            border-radius: 0.5rem;
        }
    </style>
@endpush

@section('content')

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <i class="ti ti-home-bolt me-2"></i>
                 <a href="{{route('admin.home')}}">@lang('trans.home')</a>
            </li>
            <li class="breadcrumb-item">
                <i class="ti ti-settings me-2"></i>
                <a href="{{route('admin.settings.index')}}">@lang('trans.settings.index')</a>
            </li>
        </ol>
    </nav>

    <div class="card mb-4 mt-4 shadow-sm">

        <div class="col-12 mb-4">
            <!-- Modern Nav Pills Tabs -->
            <ul class="nav nav-pills mt-4" id="settingsTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab" aria-controls="general" aria-selected="true">
                        <i class="ti ti-user-circle"></i> @lang('trans.settings.general')
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="media-tab" data-bs-toggle="tab" data-bs-target="#media" type="button" role="tab" aria-controls="media" aria-selected="false">
                        <i class="ti ti-photo"></i> @lang('trans.settings.media')
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="terms-tab" data-bs-toggle="tab" data-bs-target="#terms" type="button" role="tab" aria-controls="terms" aria-selected="false">
                        <i class="ti ti-file-text"></i> @lang('trans.settings.terms_conditions')
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="about_us-tab" data-bs-toggle="tab" data-bs-target="#about_us" type="button" role="tab" aria-controls="about_us" aria-selected="false">
                        <i class="ti ti-file-text"></i> @lang('trans.settings.about_us')
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="kill_screen_text-tab" data-bs-toggle="tab" data-bs-target="#kill_screen_text" type="button" role="tab" aria-controls="kill_screen_text" aria-selected="false">
                        <i class="ti ti-file-text"></i> @lang('trans.settings.kill_screen_text')
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="privacy_policy-tab" data-bs-toggle="tab" data-bs-target="#privacy_policy" type="button" role="tab" aria-controls="privacy_policy" aria-selected="false">
                        <i class="ti ti-file-text"></i> @lang('trans.settings.privacy_policy')
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="switches-tab" data-bs-toggle="tab" data-bs-target="#switches" type="button" role="tab" aria-controls="switches" aria-selected="false">
                        <i class="ti ti-toggle-left"></i> @lang('trans.settings.switches')
                    </button>
                </li>
            </ul>
            <form class="card-body form validated-form" method="POST"
                  action="{{route('admin.settings.update')}}" novalidate enctype="multipart/form-data">
                @csrf
                <div class="tab-content pt-3" id="settingsTabContent">
                    <!-- General Tab -->
                    <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                        <div class="row g-4">
                            <x-admin.input :value="globalSetting('name_ar')" required="true" name="name_ar"
                                           label="name_ar"
                                           type="text" col="col-xl-6" placeholder="name_ar"
                            />
                            <x-admin.input :value="globalSetting('name_en')" required="true" name="name_en"
                                           label="name_en"
                                           type="text" col="col-xl-6" placeholder="name_en"
                            />
                            <x-admin.input :value="globalSetting('email')" required="true" name="email"
                                           label="email"
                                           type="email" col="col-xl-6" placeholder="email"
                            />
                            <x-admin.input :value="globalSetting('whatsapp')" required="true" name="whatsapp"
                                           label="whatsapp"
                                           type="text" col="col-xl-6" placeholder="whatsapp"
                            />
                            <x-admin.input :value="globalSetting('phone')" required="true" name="phone"
                                           label="phone"
                                           type="text" col="col-xl-6" placeholder="phone"
                            />



                            <x-admin.input :value="globalSetting('different_gold_price')" required="true" name="different_gold_price"
                                           label="different_gold_price"
                                           type="number" col="col-xl-6" placeholder="different_gold_price"
                            />
                        </div>
                    </div>
                    <!-- Media Tab -->
                    <div class="tab-pane fade" id="media" role="tabpanel" aria-labelledby="media-tab">
                        <div class="row g-4">
                            <x-admin.file
                                :files="globalSetting('logo_ar')"
                                name="logo_ar"
                                class="col-3"
                                :multiple="false"
                                id="logo_ar"
                            />
                            <x-admin.file
                                :files="globalSetting('logo_en')"
                                name="logo_en"
                                class="col-3"
                                :multiple="false"
                                id="logo_en"
                            />
                            <x-admin.file
                                :files="globalSetting('fav_icon')"
                                name="fav_icon"
                                class="col-3"
                                :multiple="false"
                                id="fav_icon"
                            />
                            <x-admin.file
                                :files="globalSetting('login_background')"
                                name="login_background"
                                class="col-3"
                                :multiple="false"
                                id="login_background"
                            />
                            <x-admin.file
                                :files="globalSetting('no_data_icon')"
                                name="no_data_icon"
                                class="col-3"
                                :multiple="false"
                                id="no_data_icon"
                            />
                            <x-admin.file
                                :files="globalSetting('default_user')"
                                name="default_user"
                                class="col-3"
                                :multiple="false"
                                id="default_user"
                            />
                        </div>
                    </div>
                    <div class="tab-pane fade" id="terms" role="tabpanel" aria-labelledby="terms-tab">
                        <div class="row g-4">
                            @foreach( languages() as $lang)
                                <x-admin.textarea
                                    :value="globalSetting('terms_conditions_'.$lang)"
                                    name="terms_conditions_{{$lang}}"
                                    label="terms_conditions_{{$lang}}"
                                    placeholder="enter_terms_conditions_{{$lang}}"
                                    col="col-12"
                                    height="400"
                                    required="true"
                                />
                            @endforeach
                        </div>
                    </div>
                    <div class="tab-pane fade" id="about_us" role="tabpanel" aria-labelledby="about_us-tab">
                        <div class="row g-4">
                            @foreach( languages() as $lang)
                                <x-admin.textarea
                                    :value="globalSetting('about_us_'.$lang)"
                                    name="about_us_{{$lang}}"
                                    label="about_us_{{$lang}}"
                                    placeholder="enter_about_us_{{$lang}}"
                                    col="col-12"
                                    height="400"
                                    required="true"
                                />
                            @endforeach
                        </div>
                    </div>
                    <div class="tab-pane fade" id="kill_screen_text" role="tabpanel" aria-labelledby="kill_screen_text-tab">
                        <div class="row g-4">
                            @foreach( languages() as $lang)
                                <x-admin.textarea
                                    :value="globalSetting('kill_screen_text_'.$lang)"
                                    name="kill_screen_text_{{$lang}}"
                                    label="kill_screen_text_{{$lang}}"
                                    placeholder="enter_kill_screen_text_{{$lang}}"
                                    col="col-12"
                                    height="400"
                                    required="true"
                                />
                            @endforeach
                        </div>
                    </div>
                    <div class="tab-pane fade" id="privacy_policy" role="tabpanel" aria-labelledby="privacy_policy-tab">
                        <div class="row g-4">
                            @foreach( languages() as $lang)
                                <x-admin.textarea
                                    :value="globalSetting('privacy_policy_'.$lang)"
                                    name="privacy_policy_{{$lang}}"
                                    label="privacy_policy_{{$lang}}"
                                    placeholder="enter_privacy_policy_{{$lang}}"
                                    col="col-12"
                                    height="400"
                                    required="true"
                                />
                            @endforeach
                        </div>
                    </div>

                    <!-- Switches Tab -->
                    <div class="tab-pane fade" id="switches" role="tabpanel" aria-labelledby="switches-tab">
                        <div class="row g-4">
                            <x-admin.toggle-switch
                                :value="globalSetting('is_killed_screen')"
                                name="is_killed_screen"
                                label="is_killed_screen"
                                class="success"
                                col="col-xl-6"
                            />
                        </div>
                    </div> 
                    <div class="row g-4">
                        <div class="col-xl-6">
                            <input type="hidden" id="egrates-gold-url" value="{{ route('admin.egrates.cache-gold') }}">
                            <input type="hidden" id="egrates-csrf" value="{{ csrf_token() }}">
                            <button type="button" id="egrates-gold-btn" class="btn btn-primary modern-btn">
                                <i class="ti ti-coin"></i> @lang('trans.egrates.update_gold')
                            </button>
                        </div>
                        <div class="col-xl-6">
                            <div class="mb-2">
                                <label class="form-label">@lang('trans.egrates.currencies_placeholder')</label>
                                <input type="text" id="egrates-codes" class="form-control" placeholder="USD,EUR,GBP,AED">
                                <input type="hidden" id="egrates-currencies-url" value="{{ route('admin.egrates.cache-currencies') }}">
                            </div>
                            <button type="button" id="egrates-currencies-btn" class="btn btn-primary modern-btn">
                                <i class="ti ti-currency"></i> @lang('trans.egrates.update_currencies')
                            </button>
                        </div>
                    </div>
                </div>
                </div>
                <div class="pt-4 d-flex justify-content-center mt-3 gap-3">
                    @can('update-setting')
                    <button type="submit"
                            class="btn btn-primary modern-btn me-sm-3 me-1 waves-effect waves-light submit-button">
                        <i class="ti ti-edit"></i> @lang('trans.edit')
                    </button>
                    @endcan
                    <a class="btn btn-label-dribbble modern-btn waves-effect"
                       href="{{ url()->previous()}}">
                        <i class="ti ti-arrow-left"></i> @lang('trans.back')
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('js_files')
    <!-- Remove stepper JS, add Bootstrap tab JS if not already included -->
    @include('dashboard.shared.submitEditForm')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const csrf = document.getElementById('egrates-csrf')?.value;

            function postJson(url, payload) {
                return fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrf
                    },
                    body: JSON.stringify(payload || {})
                }).then(res => res.json());
            }

            const goldBtn = document.getElementById('egrates-gold-btn');
            if (goldBtn) {
                goldBtn.addEventListener('click', function () {
                    const url = document.getElementById('egrates-gold-url').value;
                    postJson(url).then(json => {
                        const status = json.status === 'success' ? 'success' : 'error';
                        const message = json.message || (status === 'success' ? '{{ __('trans.success') }}' : '{{ __('trans.error') }}');
                        if (window.Swal) {
                            Swal.fire({ icon: status, title: message });
                        } else {
                            alert(message);
                        }
                    }).catch(() => {
                        if (window.Swal) Swal.fire({ icon: 'error', title: '{{ __('trans.error') }}' });
                        else alert('{{ __('trans.error') }}');
                    });
                });
            }

            const currenciesBtn = document.getElementById('egrates-currencies-btn');
            if (currenciesBtn) {
                currenciesBtn.addEventListener('click', function () {
                    const url = document.getElementById('egrates-currencies-url').value;
                    const codesStr = document.getElementById('egrates-codes').value || '';
                    const codes = codesStr
                        .split(',')
                        .map(s => s.trim())
                        .filter(Boolean);
                    postJson(url, { codes }).then(json => {
                        const status = json.status === 'success' ? 'success' : 'error';
                        const message = json.message || (status === 'success' ? '{{ __('trans.success') }}' : '{{ __('trans.error') }}');
                        if (window.Swal) {
                            Swal.fire({ icon: status, title: message });
                        } else {
                            alert(message);
                        }
                    }).catch(() => {
                        if (window.Swal) Swal.fire({ icon: 'error', title: '{{ __('trans.error') }}' });
                        else alert('{{ __('trans.error') }}');
                    });
                });
            }
        });
    </script>
@endpush
