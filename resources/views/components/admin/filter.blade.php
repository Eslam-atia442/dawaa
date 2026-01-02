<div class="filter_div row mt-4 py-3 gap-3 gap-md-0" style="display: none;">

    @isset($order)
        <div class="col-md-2 mb-2">
            <label for="defaultFormControlInput" class="form-label">{{ __('trans.sort_by') }}</label>
            <select name="order" id="ProductCategory" class="select2 form-select text-capitalize search-input">
                <option value>{{ __('trans.choose') }}</option>
                <option value="ASC">{{ __('trans.Progressive') }}</option>
                <option value="DESC" selected>{{ __('trans.descending') }}</option>
            </select>
          </div>
    @endisset

    @isset($datefilter)
        <div class="col-md-2 mb-3">
            <label for="defaultFormControlInput" class="form-label">{{ __('trans.beginning_date') }}</label>
            <input type="date"  name="createdAtMin" class="form-control search-input" id="" placeholder="{{ __('trans.beginning_date') }}">
        </div>

        <div class="col-md-2 mb-3">
            <label for="defaultFormControlInput" class="form-label">{{ __('trans.end_date') }}</label>
            <input type="date"  name="createdAtMax" class="form-control search-input" id="\" placeholder="{{ __('trans.end_date') }}">
        </div>
    @endisset

    @isset($searchArray)
        @foreach ($searchArray as $key => $value)
            @if ($value['input_type'] == 'text')
                <div class="col-md-2 mb-3 ">
                    <label for="defaultFormControlInput" class="form-label">{{ $value['input_name'] }}</label>
                    <input type="text" name="{{ $key }}" class="form-control search-input"  placeholder="{{ __('trans.write') . $value['input_name'] }}">
                </div>
            @elseif ($value['input_type'] == 'number')
                <div class="col-md-2 mb-3 ">
                    <label for="defaultFormControlInput" class="form-label">{{ $value['input_name'] }}</label>
                    <input type="number" name="{{ $key }}" class="form-control search-input"  placeholder="{{ __('trans.write') . $value['input_name'] }}">
                </div>
            @elseif ($value['input_type'] == 'date')
                <div class="col-md-2 mb-3 ">
                    <label for="defaultFormControlInput" class="form-label">{{ $value['input_name'] }}</label>
                    <input type="date" name="{{ $key }}" class="form-control search-input"  placeholder="{{ __('trans.write') . $value['input_name'] }}">
                </div>
            @elseif ($value['input_type'] == 'select')
                <div class="col-md-2 mb-2">
                    <label for="defaultFormControlInput" class="form-label">{{ $value['input_name'] }}</label>
                    <select  name="{{ $key }}" @if($value['multiple'] ?? false) multiple @endif  class="select2 search-input form-select text-capitalize">
                         @foreach ($value['rows'] as $row)
                            <option value="{{ $row['id'] }}">
                                {{ isset($value['row_name']) ? $row[$value['row_name']] : $row['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
        @endforeach
    @endisset
</div>
