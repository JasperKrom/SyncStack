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
    {--continueOnFailure : By default it stops rolling back syncs if one errors out. If syncs are order agnostic, you may want to continue to rollback the next sync if one fails.}';

    protected $description = 'Rollback last batch of sync commands.';

    public function handle(SynchronisationRepository $syncRepository): void {
        $syncs = $syncRepository->lastBatch();

        // Short circuit and inform if no syncs are found
        if(count($syncs) === 0) {
            $this->info('No synchronisations found. Nothing to rollback.');
        }

        /** @var Synchronisation $sync */
        foreach($syncs as $sync) {
            try {
                if (($instance = $this->getInstance(base_path().$sync->path)) === null) {
                    continue;
                }

                app()->call([$instance, 'rollback']);

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
