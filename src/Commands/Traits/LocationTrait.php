<?php

namespace RapideSoftware\SyncStack\Commands\Traits;

use Illuminate\Support\Str;

trait LocationTrait
{
    protected function determineLocation(string $location): string {
        return $this->option('fromBasePath') ? base_path($location) : app_path($this->parseLocation($location));
    }

    protected function parseLocation(string $location): string {
        $folders = explode(DIRECTORY_SEPARATOR, trim($location, DIRECTORY_SEPARATOR));
        $folders = array_map(fn ($folder) => Str::studly($folder), $folders);
        return Str::finish(Str::start(implode(DIRECTORY_SEPARATOR, $folders), DIRECTORY_SEPARATOR), DIRECTORY_SEPARATOR);
    }
}
