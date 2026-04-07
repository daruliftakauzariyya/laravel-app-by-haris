<?php

namespace App\Console\Commands;

use App\Models\Fatwa;
use App\Models\FatwaViewSnapshot;
use Illuminate\Console\Command;

class SnapshotFatwaViews extends Command
{
    protected $signature   = 'fatwa:snapshot-views';
    protected $description = 'Take a 45-day read count snapshot for all published fatwas';

    public function handle(): void
    {
        $today  = today()->toDateString();
        $fatwas = Fatwa::whereNotNull('published_at')->get();

        $count = 0;
        foreach ($fatwas as $fatwa) {
            FatwaViewSnapshot::updateOrCreate(
                ['fatwa_id' => $fatwa->id, 'snapshot_date' => $today],
                ['views'    => $fatwa->views]
            );
            $count++;
        }

        $this->info("Snapshots recorded for {$count} fatwa(s) on {$today}.");
    }
}
