<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\SyncPermissions;
use App\Models\Module;
use App\Modules\ModuleManager;
use Illuminate\Console\Command;

class SyncModules extends Command
{
    protected $signature = 'modules:sync';

    protected $description = 'Gleicht die modules-Tabelle und die Berechtigungen mit den module.json-Manifesten ab';

    public function handle(ModuleManager $moduleManager, SyncPermissions $syncPermissions): int
    {
        foreach ($moduleManager->manifests() as $manifest) {
            Module::query()->updateOrCreate(
                ['code' => $manifest->code],
                [
                    'name' => $manifest->name,
                    'description' => $manifest->description,
                    'is_core' => $manifest->isCore,
                ],
            );

            $this->info("Modul synchronisiert: {$manifest->code}");
        }

        $syncPermissions->execute();

        $this->info('Berechtigungen synchronisiert.');

        return self::SUCCESS;
    }
}
