<li class="menu-item {{in_array(substr(Route::currentRouteName(), 6), $value['child']) ? 'open' : '' }} " style="">
    <a href="javascript:void(0);" class="menu-link menu-toggle">
        {!! $value['icon'] !!}
      <div data-i18n="Form Wizard">{{__('routes.'. $value['title'])}}</div>
    </a>
    <ul class="menu-sub">
        @foreach ($value['child'] as $child)
            @if (isset($routes_data['"admin.' . $child . '"']) && $routes_data['"admin.' . $child . '"']['title'] && $routes_data['"admin.' . $child . '"']['sub_link'])
                <li class="menu-item {{('admin.'.$child) == Route::currentRouteName() ? 'active' : ''}}">
                  <a href="{{route('admin.'.$child)}}" class="menu-link">
                    <div data-i18n="Numbered">{{ __('routes.'.$routes_data['"admin.' . $child . '"']['title'])}}</div>
                  </a>
                </li>
            @endif
        @endforeach
    </ul>
  </li>
