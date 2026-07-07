<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SnapshotFatwaViews extends Command
{
    protected $signature   = 'fatwa:snapshot-views';
    protected $description = 'Take a 45-day read count snapshot for all published fatwas (Deprecated)';

    public function handle(): void
    {
        $this->info("Snapshot command is deprecated and was skipped.");
    }
}
