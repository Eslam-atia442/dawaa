<div class="d-flex justify-content-between">

    <div class="">

        @if($addbutton && $createPermission)
            @can($createPermission)
                <a href="{{$addbutton}}" type="button" class=" me-1 btn btn-outline-primary waves-effect">
                    <span class="ti-xs ti ti-table-plus me-1"></span>{{__('trans.add')}}
                </a>
            @endcan
        @endif

         @if($deletebutton && $deletePermission)
            {{-- @can($deletePermission) --}}
            <button type="button" data-route="{{$deletebutton}}"
                    class=" me-1 btn btn-outline-danger waves-effect delete_all_button d-none">
                <span class="ti-xs ti ti-trash-off me-1"></span>{{__('trans.delete_selected')}}
            </button>
            {{-- @endcan --}}
        @endif

        @isset($extrabuttons)
            {{$extrabuttonsdiv}}
        @endif
        <button type="button" class="reloadTable me-1 btn btn-outline-success waves-effect">
            <span class="ti-xs ti ti-reload me-1 reloadTable"></span>{{__('trans.refresh')}}
        </button>
    </div>

    <div>
        <button type="button" class="btn btn-outline-info waves-effect show_filter">
            <span class="ti-xs ti ti-filter-plus me-1"></span>
        </button>
    </div>

</div>
