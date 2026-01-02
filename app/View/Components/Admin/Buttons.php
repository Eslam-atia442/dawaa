<?php

namespace App\View\Components\Admin;

use Illuminate\View\Component;

class Buttons extends Component
{
    public $addbutton ;
    public $deletebutton ;
    public $extrabuttons ;
    public $createPermission ;
    public $deletePermission ;

    public function __construct($addbutton = null ,
                                $extrabuttons = null ,
                                $deletebutton = null,
                                $createPermission = null ,
                                $deletePermission = null )
    {
        $this->addbutton    = $addbutton    ;
        $this->extrabuttons = $extrabuttons ;
        $this->deletebutton = $deletebutton ;
        $this->createPermission = $createPermission ;
        $this->deletePermission = $deletePermission ;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.admin.buttons');
    }
}
