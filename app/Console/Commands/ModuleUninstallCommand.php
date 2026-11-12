<?php

namespace App\Console\Commands;

use App\Kernel\ModuleInstaller;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('module:uninstall {slug} {--as=manual} {--identifier=owner}')]
#[Description('Remove one referrer from a module through the kernel. Orphans it if that was the last one — never deletes its data.')]
class ModuleUninstallCommand extends Command
{
    public function handle(ModuleInstaller $installer): int
    {
        $slug = $this->argument('slug');

        $installer->uninstall($slug, $this->option('as'), $this->option('identifier'));

        $this->info("Removed referrer (referrer: {$this->option('as')}/{$this->option('identifier')}) from [{$slug}].");

        return self::SUCCESS;
    }
}
