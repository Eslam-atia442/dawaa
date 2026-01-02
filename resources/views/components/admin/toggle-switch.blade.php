<div class="mb-3 form-group {{$col}}">
    <label class="form-label">{{__('trans.'.$label)}}</label>
    <div class="d-flex align-items-center">
        <!-- Hidden input to ensure value 0 is sent when unchecked -->
        <input type="hidden" name="{{$name}}" value="0">
        <label class="switch switch-{{$class}} switch-lg">
            <input type="checkbox" 
                   class="switch-input" 
                   name="{{$name}}" 
                   id="{{$name}}" 
                   value="1"
                   @if ($value == 1 || $value === true || $value === '1')
                       checked
                   @endif
                   @if ($required)
                       required
                   @endif>
            <span class="switch-toggle-slider">
                <span class="switch-on">
                    <i class="ti ti-check"></i>
                </span>
                <span class="switch-off">
                    <i class="ti ti-x"></i>
                </span>
            </span>
        </label>
    </div>
</div>
