<?php

namespace Cptbadcode\LaravelPager\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Pluralizer;
use Illuminate\Support\Str;

trait CommandFileCreator
{
    protected Filesystem $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $path = $this->getSourceFilePath();

        $this->makeDirectory(dirname($path));

        $contents = $this->getSourceFile();

        if (!$this->files->exists($path)) {
            $this->files->put($path, $contents);
            $this->info("File : {$path} created");

            return Command::SUCCESS;
        } else {
            $this->warn("File : {$path} already exits");
            return Command::INVALID;
        }
    }

    /**
     * Return the stub file path
     * @return string
     *
     */
    public function getStubPath(): string
    {
        return __DIR__. '/../../Stubs/'.$this->stubPath;
    }

    /**
     **
     * Map the stub variables present in stub to its value
     *
     * @return array
     *
     */
    public function getStubVariables(): array
    {
        $classNamePart = explode(DIRECTORY_SEPARATOR, Str::replace(
            '/',
            DIRECTORY_SEPARATOR,
            $this->getSingularClassName($this->argument('name')).$this->prefix
        ));
        $className = array_pop($classNamePart);
        return [
            'NAMESPACE'         => implode(DIRECTORY_SEPARATOR, array_merge([$this->fileNamespace], $classNamePart)),
            'CLASS_NAME'        => $className,
            'KEY'               => Str::snake($className),
            'TITLE'             => $this->option('title') ?? $className
        ];
    }

    /**
     * Get the stub path and the stub variables
     *
     * @return string|array|bool
     *
     */
    public function getSourceFile(): string|array|bool
    {
        return $this->getStubContents($this->getStubPath(), $this->getStubVariables());
    }


    /**
     * Replace the stub variables(key) with the desire value
     *
     * @param string $stub
     * @param array $stubVariables
     * @return string|array|bool
     */
    public function getStubContents(string $stub , array $stubVariables = []): string|array|bool
    {
        $contents = file_get_contents($stub);

        foreach ($stubVariables as $search => $replace)
        {
            $contents = str_replace('{{ '.$search.' }}' , $replace, $contents);
        }

        return $contents;

    }

    /**
     * Get the full path of generate class
     *
     * @return string
     */
    public function getSourceFilePath(): string
    {
        return base_path($this->fileNamespace) .
               DIRECTORY_SEPARATOR .
               $this->getSingularClassName($this->argument('name')) .
               $this->prefix . '.php';
    }

    /**
     * Return the Singular Capitalize Name
     * @param $name
     * @return string
     */
    public function getSingularClassName($name): string
    {
        return ucwords(Pluralizer::singular($name));
    }

    /**
     * Build the directory for the class if necessary.
     *
     * @param string $path
     * @return string
     */
    protected function makeDirectory(string $path): string
    {
        if (! $this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0777, true, true);
        }

        return $path;
    }
}
