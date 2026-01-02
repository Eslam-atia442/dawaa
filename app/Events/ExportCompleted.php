<?php

namespace App\Events;

use App\Models\Export;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ExportCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Export $export;

    /**
     * Create a new event instance.
     */
    public function __construct(Export $export)
    {
        $this->export = $export;
    }
}
