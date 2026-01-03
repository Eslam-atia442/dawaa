<li class="menu-header small text-uppercase">
    <span class="menu-header-text"> @lang('trans.admin.index') - @lang('trans.user.index') </span>
</li>
@can('read-all-admin')
    <li class="menu-item @if(Route::currentRouteName() == 'admin.admins.index') active @endif">
        <a href="{{route('admin.admins.index')}}" class="menu-link">
            <i class="menu-icon ti ti-user-bolt"></i>
            <div data-i18n="@lang('trans.admins')">@lang('trans.admin.index')</div>
        </a>
    </li>
@endcan

@can('read-all-user')
    <li class="menu-item @if(Route::currentRouteName() == 'admin.users.index') active @endif">
        <a href="{{route('admin.users.index')}}" class="menu-link">
            <i class="menu-icon tf-icons ti  ti-user"></i>
            <div data-i18n="@lang('trans.users')">@lang('trans.user.index')</div>
        </a>
    </li>
@endcan

<li class="menu-header small text-uppercase">
    <span class="menu-header-text">@lang('trans.country.index') </span>
</li>
@can('read-all-country')
    <li class="menu-item @if(Route::currentRouteName() == 'admin.countries.index') active @endif">
        <a href="{{route('admin.countries.index')}}" class="menu-link">
            <i class="menu-icon tf-icons ti  ti-building-skyscraper"></i>
            <div data-i18n="@lang('trans.countries')">@lang('trans.country.index')</div>
        </a>
    </li>
@endcan

@can('read-all-region')
    <li class="menu-item @if(Route::currentRouteName() == 'admin.regions.index') active @endif">
        <a href="{{route('admin.regions.index')}}" class="menu-link">
            <i class="menu-icon tf-icons ti  ti-directions"></i>
            <div data-i18n="@lang('trans.regions')">@lang('trans.region.index')</div>
        </a>
    </li>
@endcan

@can('read-all-city')
    <li class="menu-item @if(Route::currentRouteName() == 'admin.cities.index') active @endif">
        <a href="{{route('admin.cities.index')}}" class="menu-link">
            <i class="menu-icon tf-icons ti ti-building-community"></i>
            <div data-i18n="@lang('trans.cities')">@lang('trans.city.index')</div>
        </a>
    </li>
@endcan


<li class="menu-header small text-uppercase">
    <span class="menu-header-text">@lang('trans.content') </span>
</li>




@can('read-all-category')
    <li class="menu-item @if(Route::currentRouteName() == 'admin.categories.index') active @endif">
        <a href="{{route('admin.categories.index')}}" class="menu-link">
            <i class="menu-icon tf-icons ti  ti-category-2"></i>
            <div data-i18n="@lang('trans.categories')">@lang('trans.category.index')</div>
        </a>
    </li>
@endcan


@can('read-all-brand')
    <li class="menu-item @if(Route::currentRouteName() == 'admin.brands.index') active @endif">
        <a href="{{route('admin.brands.index')}}" class="menu-link">
            <i class="menu-icon tf-icons ti  ti-brand-4chan"></i>
            <div data-i18n="@lang('trans.brands')">@lang('trans.brand.index')</div>
        </a>
    </li>
@endcan

{{--#new_comand_side_bar_element_here--}}





<li class="menu-header small text-uppercase">
    <span class="menu-header-text">@lang('trans.setting.index')</span>
</li>
@can('fcm-setting')
<li class="menu-item @if(Route::currentRouteName() == 'admin.fcm-notifications.index') active @endif">
    <a href="{{route('admin.fcm-notifications.index')}}" class="menu-link">
        <i class="menu-icon tf-icons ti ti-bell"></i>
        <div data-i18n="@lang('trans.fcm_notifications')">@lang('trans.fcm_notifications')</div>
    </a>
</li>
@endcan
@can('read-all-activity-log')
    <li class="menu-item @if(Route::currentRouteName() == 'admin.activity-log') active @endif">
        <a href="{{route('admin.activity-log')}}" class="menu-link">
            <i class="menu-icon tf-icons ti  ti-abacus"></i>
            <div data-i18n="@lang('trans.activityLog.index')">@lang('trans.activityLog.index')</div>
        </a>
    </li>
@endcan


@can('read-all-export')
    <li class="menu-item @if(Route::currentRouteName() == 'admin.exports.index') active @endif">
        <a href="{{route('admin.exports.index')}}" class="menu-link">
            <i class="menu-icon ti ti-file-export"></i>
            <div data-i18n="@lang('trans.export_management')">@lang('trans.export_management')</div>
        </a>
    </li>
@endcan

@can('read-all-role')
    <li class="menu-item @if(Route::currentRouteName() == 'admin.roles.index') active @endif">
        <a href="{{route('admin.roles.index')}}" class="menu-link">
            <i class="menu-icon  ti ti-fingerprint"></i>
            <div data-i18n="@lang('trans.roles')">@lang('trans.role.index')</div>
        </a>
    </li>
@endcan

@can('read-all-setting')
    <li class="menu-item @if(Route::currentRouteName() == 'admin.settings.index') active @endif">
        <a href="{{route('admin.settings.index')}}" class="menu-link">
            <i class="menu-icon tf-icons ti  ti-settings"></i>
            <div data-i18n="@lang('trans.settings.index')">@lang('trans.setting.index')</div>
        </a>
    </li>
@endcan























