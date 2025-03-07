<?php

namespace RapideSoftware\SyncStack\Commands;

use Exception;
use RapideSoftware\SyncStack\Models\Synchronisation;
use RapideSoftware\SyncStack\Repositories\SynchronisationRepository;
use RapideSoftware\SyncStack\Commands\Base\InstantiatorCommand;
use RapideSoftware\SyncStack\Commands\Traits\LocationTrait;

class RollbackSynchronisationCommand extends InstantiatorCommand {

    use LocationTrait;

    protected $signature = 'sync:rollback
    {--location=Synchronisations : The base location where your files are placed, searched recursively. By default it is app_path().\'Synchronisations\'.}
    {--fromBasePath : The location you gave starts from the base_path() and assumes nothing about naming conventions. Otherwise it starts from app_path() and expects Str::Studly naming convention for your folders and classes.}
    {--continueOnFailure : By default it stops rolling back syncs if one errors out. If syncs are order agnostic, you may want to continue to rollback the next sync if one fails.}';

    protected $description = 'Rollback last batch of sync commands.';

    public function handle(SynchronisationRepository $syncRepository): void {
        $syncsLocation = $this->option('location');
        $directory = $this->determineLocation($syncsLocation);
        $syncs = $syncRepository->lastBatch();

        // Short circuit and inform if no syncs are found
        if(count($syncs) === 0) {
            $this->info('No synchronisations found. Nothing to rollback.');
        }

        /** @var Synchronisation $sync */
        foreach($syncs as $sync) {
            try {
                if (($instance = $this->getInstance($directory.$sync->path)) === null) {
                    continue;
                }

                $instance->rollback();
                $sync->deleteOrFail();
            } catch (Exception $exception) {
                $this->error('Something went wrong! Check your rollback code. It is possible a partial rollback has happened.');
                $this->error($exception->getMessage());
                $this->error($exception->getTraceAsString());

                if (!$this->option('continueOnFailure')) {
                    return;
                }
            }
        }
    }

    protected function hasRequiredMethod(object $instance): bool {
        return !method_exists($instance, 'rollback');
    }
}
