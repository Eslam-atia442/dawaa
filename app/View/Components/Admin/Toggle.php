<?php

namespace App\View\Components\Admin;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Toggle extends Component
{
    /**
     * Create a new component instance.
     */

    public string $url;
    public bool $checked = false;

    public function __construct($url, $checked = false)
    {
        $this->url = $url;
        $this->checked = $checked;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.admin.toggle');
    }
}
