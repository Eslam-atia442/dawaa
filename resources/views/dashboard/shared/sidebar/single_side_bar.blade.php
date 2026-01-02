<li class="menu-item {{$value['routeName'] == Route::currentRouteName() ? 'active' : ''}}">
    <a href="{{route($value['routeName'])}}" class="menu-link">
        {!! $value['icon'] !!}
        <div data-i18n="Page 1">{{__('routes.'.$value['title'])}}</div>
    </a>
</li>
  