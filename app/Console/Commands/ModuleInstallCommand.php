<?php

namespace App\Console\Commands;

use App\Kernel\ModuleInstaller;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('module:install {slug} {--as=manual} {--identifier=owner}')]
#[Description('Install a module through the kernel (manual referrer by default).')]
class ModuleInstallCommand extends Command
{
    public function handle(ModuleInstaller $installer): int
    {
        $slug = $this->argument('slug');

        $installer->install($slug, $this->option('as'), $this->option('identifier'));

        $this->info("Installed [{$slug}] (referrer: {$this->option('as')}/{$this->option('identifier')}).");

        return self::SUCCESS;
    }
}
