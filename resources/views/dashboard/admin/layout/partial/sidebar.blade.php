
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{route('admin.home')}}" class="app-brand-link">
            <img class="avatar avatar me-2 rounded-2 bg-label-secondary"
                 src="{{ optional(globalSetting('logo_' . app()->getLocale())->first())->getUrl() ?? asset('/assets/img/avatars/default.png') }}"
                 alt="">

        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-middle"></i>
            <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Page -->

      @include('dashboard.admin.layout.partial.sidebarItems')

    </ul>
</aside>

