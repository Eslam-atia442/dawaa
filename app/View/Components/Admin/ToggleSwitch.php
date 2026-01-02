<?php

namespace App\View\Components\Admin;

use Illuminate\View\Component;

class ToggleSwitch extends Component
{
    public $name;
    public $value;
    public $label;
    public $class;
    public $col;
    public $required;

    public function __construct(
        $name = null,
        $value = 0,
        $label = null,
        $class = 'success',
        $col = 'col-md-6',
        $required = false
    ) {
        $this->name = $name;
        $this->value = $value;
        $this->label = $label;
        $this->class = $class;
        $this->col = $col;
        $this->required = $required;
    }

    public function render()
    {
        return view('components.admin.toggle-switch');
    }
}
