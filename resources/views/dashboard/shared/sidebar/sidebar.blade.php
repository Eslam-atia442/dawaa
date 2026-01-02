@if (auth('admin')->user()->type == 'super_admin')
      
    @foreach ($routes_data as $key => $value)

        @if ($value['type'] == 'parent') 

            @if ($value['has_sub_route'] && $value['child'] ) 
                @include('admin.shared.sidebar.dropdown', compact('value' , 'routes_data'))
            @else
                @include('admin.shared.sidebar.single_side_bar' , compact('value'))
            @endif

        @endif

    @endforeach
@else
    
    @foreach ($routes_data as $key => $value)

        @if ($value['type'] == 'parent') 

            @if ( in_array( $value['routeName'] , $my_routes) && $value['has_sub_route'] && $value['child'] ) 
            
                @include('admin.shared.sidebar.dropdown', compact('value' , 'routes_data'))
            
            @elseif(in_array( $value['routeName'] , $my_routes))
                
                @include('admin.shared.sidebar.single_side_bar' , compact('value'))
            
            @endif

        @endif

    @endforeach
@endif