<?php

namespace App\View\Components\Admin;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use function Symfony\Component\Translation\t;

class File extends Component
{

    public string $type;
    public string $guard = 'admin';
    public string $class;
    public string $title;
    public string $name;
    public int $maxFiles;
    public bool $multiple = false;
    public mixed $files = null;
    public mixed $accept = null;


    public function __construct($type = 'file',
                                $guard = 'admin',
                                $class = 'col-12',
                                $name = 'image',
                                $maxFiles = 999,
                                $multiple = false,
                                $files = null,
                                $accept = 'image/*')
    {
        $this->type = $type;
        $this->guard = $guard;
        $this->class = $class;
        $this->name = $name;
        $this->maxFiles = $maxFiles;
        $this->multiple = $multiple;
        $this->files = $files;
        $this->title = $name;
        if (!$this->multiple)
            $this->maxFiles = 1;
        else
            $this->name .= '[]';

        $this->accept = $accept;

    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.admin.file');
    }
}
