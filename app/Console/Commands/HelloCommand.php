<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;

class HelloCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hello:time';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Print hello with current time';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $currentTime = Carbon::now()->format('Y-m-d H:i:s');
        info('hello! current time is: ' . $currentTime);
        $this->info("Hello! Current time is: {$currentTime}");
    }
}
