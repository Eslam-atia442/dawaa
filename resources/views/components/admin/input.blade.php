@if (in_array($type, ['number', 'email' , 'password', 'text' ,'date' ,'url', 'time']))
    <div class="mb-3 form-group {{$col}}">
        <label for="{{$name }}" class="form-label">{{__('trans.'.$label)}}@if($required) <span class="text-danger">*</span> @endif</label>
        <input id="{{$name }}" type="{{$type}}"  @if($disabled) disabled @endif   class="form-control"
        @if ($value)
            value="{{$value}}"
        @endif
        @if ($required)
            required
        @endif
        @if ($required_message && $required)
            data-validation-required-message="{{ $required_message}}"
        @elseif ($required)
            data-validation-required-message="{{__('trans.this_field_is_required')}}"
        @endif
        @if ($type == 'email')
            data-validation-email-message="{{ __('trans.email_formula_is_incorrect') }}"
        @endif
        @if ($type == 'number')
            data-validation-number-message="{{ __('trans.the_phone_number_ must_not_have_charachters_or_symbol') }}"
        @endif
        @if ($minLength)
            minlength="{{$minLength}}"
            data-validation-minLength-message="{{ __('trans.min_length', ['number' => $minLength])  }}"
        @endif
        @if ($minLength)
            maxLength="{{$maxLength}}"
            data-validation-maxLength-message="{{ __('trans.max_length', ['number' => $maxLength])  }}"
        @endif

        name="{{$name}}" placeholder="{{__('trans.enter')}} {{__('trans.'.$placeholder)}}" />
    </div>
@elseif ($type == 'textarea')
     <div class="{{$col}}">
        <label class="form-label" for="multicol-email">{{__('trans.'.$label)}}@if($required) <span class="text-danger">*</span> @endif</label>
        <textarea id="autosize-demo" class="form-control" style="overflow: hidden; overflow-wrap: break-word; resize: none; text-align: start; min-height: 82.6px;" name="{{$name}}" placeholder="{{__('trans.enter')}} {{__('trans.'.$placeholder)}}"
        @if ($required)
            required
        @endif
        @if ($required_message && $required)
            data-validation-required-message="{{ $required_message}}"
        @elseif ($required)
            data-validation-required-message="{{__('trans.this_field_is_required')}}"
        @endif
        @if ($minLength)
            minlength="{{$minLength}}"
            data-validation-minLength-message="{{ __('trans.min_length', ['number' => $minLength])  }}"
        @endif
        @if ($maxLength)
            maxLength="{{$maxLength}}"
            data-validation-maxLength-message="{{ __('trans.max_length', ['number' => $maxLength])  }}"
        @endif
        @if ($rows)
            rows="{{$rows}}"
        @endif
        >@if ($value){{$value}}@endif</textarea>
    </div>
@elseif ($type == 'select')
    <div class="mb-3 form-group {{$col}}">
        <label for="{{$name}}" class="form-label">{{__('trans.'.$label)}}@if($required) <span class="text-danger">*</span> @endif</label>
        <div class="select2-primary">
            <select name="{{$name}}"
            @if ($required)
                required
                    @if ($required_message)
                        data-validation-required-message="{{ $required_message}}"
                    @else
                        data-validation-required-message="{{__('trans.this_field_is_required')}}"
                    @endif

            @endif
            @if (isset($multiple) && $multiple)
                multiple
            @endif

            id="select2Primary_{{ str_replace(['[', ']', '-'], ['_', '', '_'], $name) }}" class="select2 form-select">
                @if (!isset($multiple) || !$multiple)
                    <option value="">{{__('trans.choose')}} {{__('trans.'.$label)}}</option>
                @endif
                @foreach ($options as $option)
                    @if (isset($multiple) && $multiple)
                        <option {{(is_array($value) && in_array($option['id'], $value)) ? 'selected' : ''}} value="{{@$option['id']}}" >{{@$option['name']}}</option>
                    @else
                        <option {{(isset($value) && $value == $option['id']) ? 'selected' : ''}} value="{{@$option['id']}}" >{{@$option['name']}}</option>
                    @endif
                @endforeach
            </select>
        </div>
    </div>
@elseif ($type == 'image')
    <div class="d-flex align-items-start align-items-sm-center gap-4 image_groub ">
        <img src="{{$value ?? asset('storage/images/users/default.png')}}" alt="user-avatar" class="d-block uploadedAvatar w-px-100 h-px-100 rounded" id="uploadedAvatar">
        <div class="button-wrapper">
        @if (Request::segment(4) != 'show')
            <label for="upload_{{$name}}" class="btn btn-primary me-2 mb-3 waves-effect waves-light" tabindex="0">
                <span class="d-none d-sm-block">{{__('trans.upload_new_image')}}</span>
                <i class="ti ti-upload d-block d-sm-none"></i>
                <input type="file" id="upload_{{$name}}" class="account-file-input" required name="{{$name}}" hidden="" accept="image/png, image/jpeg">
            </label>

            <button type="button"  class="btn btn-label-secondary account-image-reset mb-3 waves-effect">
                <i class="ti ti-refresh-dot d-block d-sm-none"></i>
                <span class="d-none d-sm-block">{{__('trans.reset_image')}}</span>
            </button>
        @endif

        {{-- <div class="text-muted">متاح JPG, GIF or PNG. اكبر حجم متاح 800K</div> --}}
        </div>
    </div>
@elseif ($type == 'checkbox')
    <div class="mb-3 form-group {{$col}}">
        <label class="form-label">{{__('trans.'.$label)}}@if($required) <span class="text-danger">*</span> @endif</label>
        <div class="form-check form-switch">
            <input type="hidden" name="{{$name}}" value="0">
            <input class="form-check-input" type="checkbox" name="{{$name}}" id="{{$name}}" 
            @if ($value == 1 || $value === true || $value === '1' || $checked)
                checked
            @endif
            @if ($required)
                required
            @endif
            value="1">
            <label class="form-check-label" for="{{$name}}">
                {{__('trans.activate')}}
            </label>
        </div>
    </div>
@elseif ($type == 'map')
    <div class="form-group col-md-12 position-relative col-12 mb-5">
        <label for="" class="d-block mb-3 siz13 font-weight-bolder">{{(__('trans.location'))}}</label>
        <input name="map_desc" class="form-control position-absolute " value="{{$map_desc}}" style="width: 30% ; left: 30px; top:60px; z-index: 2;" id="searchTextField" value="" placeholder="{{(__('trans.location'))}}" name="">
        <div id="map" style="height: 400px; margin-top: 20px"></div>
        <input type="hidden" id="lat" name="lat" value="{{$lat}}">
        <input type="hidden" id="lng" name="lng" value="{{$lng}}">
    </div>
@endif

