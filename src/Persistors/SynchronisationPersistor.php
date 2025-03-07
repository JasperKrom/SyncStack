<?php

namespace RapideSoftware\SyncStack\Persistors;

use RapideSoftware\SyncStack\Models\Synchronisation;
use Throwable;

class SynchronisationPersistor
{

    /**
     * @throws Throwable
     */
    public function delete(Synchronisation $synchronisation): bool {
        return $synchronisation->deleteOrFail();
    }

    /**
     * @throws Throwable
     */
    public function create(string $path, int $batch): bool {
        return (new Synchronisation(['path' => $path, 'batch' => $batch]))->saveOrFail();
    }
}
