<?php

namespace App\View\Components\Admin;

use Illuminate\View\Component;

class Textarea extends Component {

    public $name;
    public $id;
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
    public $height;
    public $toolbar;
    public $fontNames;
    public $fontSizes;

    public function __construct(
        $name = null, $id = null, $value = null, $placeholder = null, $col = 'col-md-12',
        $label = null, $required = false, $required_message = null, $disabled = false,
        $class = null, $minLength = null, $maxLength = null, $rows = 10,
        $height = 300, $toolbar = null, $fontNames = null, $fontSizes = null
    ) {
        $this->name             = $name;
        $this->id               = $id;
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
        $this->height           = $height;
        $this->toolbar          = $toolbar;
        $this->fontNames        = $fontNames;
        $this->fontSizes        = $fontSizes;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render() {
        return view('components.admin.textarea');
    }
}
