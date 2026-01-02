<?php

namespace App\View\Components\Admin;

use Illuminate\View\Component;

class Filter extends Component
{
    public $datefilter ;
    public $searchArray ;
    public $order ;


    public function __construct( $datefilter = null , $searchArray = null , $order = null)
    {
         $this->datefilter   = $datefilter   ;
        $this->searchArray  = $searchArray  ;
        $this->order        = $order        ;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
         return view('components.admin.filter');
    }
}
