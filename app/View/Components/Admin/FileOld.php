<?php

namespace App\View\Components\Admin;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use function Symfony\Component\Translation\t;

class FileOld extends Component
{

    public string $type;
    public string $guard = 'admin';
    public string $class;
    public string $name;
     public string $id;
    public int $maxFiles;
    public bool $multiple = false;
    public mixed $files = null;


    public function __construct($type = 'file', $guard = 'admin', $class = 'col-12', $name = 'image' , $maxFiles = 999, $id = 'dropzone', $multiple = false ,$files = null)
    {
        $this->type = $type;
        $this->guard = $guard;
        $this->class = $class;
        $this->name = $name;
        $this->maxFiles = $maxFiles;
        $this->multiple = $multiple;
        $this->id = $name;
        $this->files = $files;
         if (!$this->multiple)
            $this->maxFiles = 1;
        else
            $this->name .= '[]';

    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.admin.file');
    }
}
