<?php

namespace App\View\Components\Admin;

use Illuminate\View\Component;

class Input extends Component
{

    public $type;
    public $name;
    public $value;
    public $placeholder;
    public $label;
    public $required;
    public $disabled;
    public $class;
    public $required_message;
    public $col;
    public $minLength;
    public $maxLength;
    public $rows;
    public $options;
    public $lat;
    public $lng;
    public $map_desc;
    public $multiple;

    public function __construct(
        $type = 'text', $name = null, $value = null, $placeholder = null, $col = 'col-md-6',
        $label = null, $required = false, $required_message = null, $disabled = false,
        $class = null,   $multiple = false, $minLength = null, $maxLength = null, $rows = null, $options = null, $lat = null, $lng = null, $map_desc = null
    )
    {
        $this->type             = $type;
        $this->name             = $name;
        $this->value            = $value;
        $this->placeholder      = $placeholder;
        $this->label            = $label;
        $this->required         = $required;
        $this->disabled         = $disabled;
        $this->class            = $class;
        $this->required_message = $required_message;
        $this->col              = $col;
        $this->minLength        = $minLength;
        $this->maxLength        = $maxLength;
        $this->rows             = $rows;
        $this->options          = $options;
        $this->lat              = $lat;
        $this->lng              = $lng;
        $this->map_desc         = $map_desc;
        $this->multiple         = $multiple;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.admin.input');
    }
}
