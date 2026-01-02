<?php

namespace App\Console\Commands;

use App\Repositories\Contracts\FileContract;
use App\Services\FileService;
use Illuminate\Console\Command;

class DeleteUnTrackedFilesCommand extends Command
{

    protected $signature = 'untracked_files:delete';
    protected $description = 'delete files where has no relations';

    public function handle(FileService $fileService)
    {
        $files = app(FileContract::class)->search(['untracked' => true], [], ['page' => false, 'limit' => false]);
        foreach ($files as $file) {
            $fileService->remove($file);
        }

        return $this->info('Untracked files deleted successfully');
    }
}
