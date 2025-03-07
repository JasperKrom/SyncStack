<?php

namespace RapideSoftware\SyncStack\Commands;

use Illuminate\Support\Str;
use InvalidArgumentException;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use RapideSoftware\SyncStack\Commands\Traits\LocationTrait;

class MakeSynchronisationCommand extends Command {

    use LocationTrait;

    protected $signature = 'sync:create {--name= : The name given to the synchronisation. It will be prepended with the datetime.}
    {--location=Synchronisations : The location where your files are placed. By default it is app_path().\'Synchronisations\'.}
    {--fromBasePath : The location you gave starts from the base_path() and assumes nothing about naming conventions. Otherwise it starts from app_path() and expects Str::Studly naming convention for your folders and classes.}
    {--abstractClass= : Give the full ::class name of the class you want to extends. Mainly useful for programmatically expanding this class. When empty, it assumes no inheritenace.}';

    protected $description = 'Create a sync command.';

    protected Filesystem $filesystem;

    public function __construct(Filesystem $filesystem) {
        parent::__construct();
        $this->filesystem = $filesystem;
    }

    public function handle(): void {
        // Normalize typed input to default directory input for system
        $syncsLocation = str_replace(['/', '/'], DIRECTORY_SEPARATOR, $this->option('location'));

        $directory = $this->determineLocation($syncsLocation);

        $baseClass = $this->option('abstractClass') ?? '';
        $name = $this->option('name') !== null ? Str::studly($this->option('name')) : $this->ask('What is the sync name?');

        if(empty($name)) {
            throw new InvalidArgumentException('Name is missing.');
        }

        $path = $directory.DIRECTORY_SEPARATOR.$this->getDatePrefix().'_'.$name.'.php';

        $this->filesystem->ensureDirectoryExists(dirname($path));

        $this->filesystem->put($path, $this->content($baseClass));
    }

    protected function getDatePrefix(): string {
        return date('Y_m_d_His');
    }

    public function content(string $baseClass = ''): string {
        if (!empty($baseClass)) {
            $baseClass = 'extends '.$baseClass. ' ';
        }
        return sprintf(
            "<?php

return new class %s{

    public function sync(): void {
        // TODO: Insert sync logic here - These are not wrapped in a transaction by the sync runner, do so manually if needed.
    }

    public function rollback(): void {
        // TODO: Insert rollback logic here - These are not wrapped in a transaction by the rollback runner, do so manually if needed.
    }
};", $baseClass);
    }
}
