<?php

namespace RapideSoftware\SyncStack\Commands\Base;

use ReflectionClass;
use ReflectionException;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

abstract class InstantiatorCommand extends Command
{
    protected Filesystem $filesystem;

    /**
     * Requiring a file for a second time returns a boolean instead of the file, thus we need to cache required files.
     */
    protected static array $requiredPathCache = [];

    public function __construct(Filesystem $filesystem) {
        parent::__construct();
        $this->filesystem = $filesystem;
    }

    /**
     * @throws ReflectionException
     */
    protected function getInstance(string $path): ?object {
        if(($instance = $this->initializeInstance($path)) === null) {
            $this->warn('Invalid filename. File in question: '.$path.' - Skipping.');
            return null;
        }

        if (!is_object($instance)) {
            $this->warn('Instance is not an object. File in question: '.$path.' - Skipping.');
            return null;
        }

        if($this->hasRequiredMethod($instance) || ($reflection = new ReflectionClass($instance::class))->isAbstract() || !$reflection->isAnonymous()) {
            $this->warn('Invalid class for synchronisation process found with classname '.$instance::class.' - Skipping.');
            return null;
        }

        return $instance;
    }

    protected function initializeInstance(string $path): mixed {
        $class = $this->getSyncClass($this->getSyncName($path));
        if(empty($class)) {
            return null;
        }

        if (class_exists($class) && realpath($path) === (new ReflectionClass($class))->getFileName()) {
            return new $class;
        }

        $sync = static::$requiredPathCache[$path] ??= $this->filesystem->getRequire($path);

        if (is_object($sync)) {
            return method_exists($sync, '__construct')
                ? $this->filesystem->getRequire($path)
                : clone $sync;
        }

        return new $class;
    }

    protected function getSyncName($path): string {
        return str_replace('.php', '', basename($path));
    }

    protected function getSyncClass(string $syncName): string
    {
        return Str::studly(implode('_', array_slice(explode('_', $syncName), 4)));
    }

    protected function getSyncPath(string $realPath, string $location): string {
        return Str::after($realPath, $location);
    }

    // Abstract functions
    abstract protected function hasRequiredMethod(object $instance);

}
