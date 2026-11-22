<?php

namespace App\Console\Commands;

use App\Kernel\FederationFetcher;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('federation:fetch')]
#[Description('Pull subscribed content from every federation peer this instance has added.')]
class FetchFederationPeersCommand extends Command
{
    public function handle(FederationFetcher $fetcher): int
    {
        $count = $fetcher->fetchAll();

        $this->info("Fetched {$count} item(s) across all federation peers.");

        return self::SUCCESS;
    }
}
