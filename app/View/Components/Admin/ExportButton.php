<?php

namespace App\View\Components\Admin;

use Illuminate\View\Component;

class ExportButton extends Component
{
    public string $route;
    public string $buttonId;
    public string $buttonClass;
    public string $iconClass;
    public ?string $label;
    public string $method;
    public array $data;

    public function __construct(
        string $route,
        string $buttonId = 'exportBtn',
        string $buttonClass = 'btn btn-outline-success waves-effect',
        string $iconClass = 'ti ti-file-export me-1',
        ?string $label = null,
        string $method = 'POST',
        array $data = []
    ) {
        $this->route = $route;
        $this->buttonId = $buttonId;
        $this->buttonClass = $buttonClass;
        $this->iconClass = $iconClass;
        $this->label = $label ?? __('trans.export_excel');
        $this->method = strtoupper($method);
        $this->data = $data;
    }

    public function render()
    {
        return view('components.admin.export-button');
    }
}

