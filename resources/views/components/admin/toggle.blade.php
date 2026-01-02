<div>
    <label class="switch switch-square switch-lg">
        <input type="checkbox" onclick="toggleField(`{{$url}}`)" class="switch-input" {{ $checked ? 'checked' : '' }} >
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

