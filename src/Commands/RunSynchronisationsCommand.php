<?php

namespace RapideSoftware\SyncStack\Commands;

use Exception;
use RapideSoftware\SyncStack\Persistors\SynchronisationPersistor;
use RapideSoftware\SyncStack\Repositories\SynchronisationRepository;
use RapideSoftware\SyncStack\Commands\Base\InstantiatorCommand;
use RapideSoftware\SyncStack\Commands\Traits\LocationTrait;

class RunSynchronisationsCommand extends InstantiatorCommand
{
    use LocationTrait;

    protected $signature = 'sync:run
    {--location=Synchronisations : The base location where your files are placed, searched recursively. By default it is app_path().\'Synchronisations\'.}
    {--fromBasePath : The location you gave starts from the base_path() and assumes nothing about naming conventions. Otherwise it starts from app_path() and expects Str::Studly naming convention for your folders and classes.}
    {--continueOnFailure : By default it stops doing syncs if one errors out. If syncs are order agnostic, you may want to continue to the next sync if one fails.}';

    protected $description = 'Run all new sync commands.';

    public function handle(SynchronisationRepository $syncRepository, SynchronisationPersistor $syncPersistor): void {
        $directory = $this->determineLocation($this->option('location'));

        $currentBatch = $syncRepository->lastBatchNumber() + 1;

        foreach ($this->filesystem->allFiles($directory) as $file) {
            $syncPath = $this->getSyncPath($file->getRealPath(), base_path());
            $hasSynced = false;

            if (!$syncRepository->hasRun($syncPath)) {
                try {
                    // Grab a runnable instance of the file and check if it is a valid instance we actually want to run
                    if (($instance = $this->getInstance($file->getRealPath())) === null) {
                        continue;
                    }

                    app()->call([$instance, 'sync']);
                    $hasSynced = true;

                    // Update the database so it knows the sync has run
                    $syncPersistor->create($syncPath, $currentBatch);

                    $this->info('Synchronised '.$instance::class);

                } catch (Exception $exception) {
                    $this->error($exception->getMessage());
                    $this->error($exception->getTraceAsString());

                    if($hasSynced) {
                        $this->error('Sync process has failed after running actual sync command successfully.');
                        $this->error('Most likely culprit is saving the synchronisation to database.');
                        $this->error('Tried to save path '.$syncPath.' for batch '. ($currentBatch));
                    }
                    if (!$this->option('continueOnFailure')) {
                        return;
                    }
                }
            }
        }
    }

    protected function hasRequiredMethod(object $instance): bool {
        return !method_exists($instance, 'sync');
    }
}
