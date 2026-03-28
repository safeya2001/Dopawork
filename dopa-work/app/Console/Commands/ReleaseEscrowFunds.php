<?php

namespace App\Console\Commands;

use App\Services\EscrowService;
use App\Services\WalletService;
use Illuminate\Console\Command;

class ReleaseEscrowFunds extends Command
{
    protected $signature = 'escrow:release';
    protected $description = 'Auto-release escrow funds for completed orders past the release window';

    public function handle(): int
    {
        $service = new EscrowService(new WalletService());
        $released = $service->processAutoReleases();

        $this->info("Auto-released escrow funds for {$released} order(s).");

        return 0;
    }
}
