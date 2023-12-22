<?php

declare(strict_types=1);

namespace VitesseCms\Database\Traits;

trait TraitRepositoryListener
{
    private ?string $repositoryClass = null;

    /**
     * @deprecated parseGetRepository can figure out the repository class
     */
    private function setRepositoryClass(string $class): void
    {
        $this->repositoryClass = str_replace(['Models'], ['Repositories'], $class . 'Repository');
    }

    private function parseGetRepository()
    {
        if ($this->repositoryClass === null) {
            $class = str_replace(['Listeners', 'Listener', 'Models\\'],
                ['Repositories', 'Repository', ''],
                self::class);

            return new $class();
        }
        
        if (class_exists($this->repositoryClass)) {
            return new $this->repositoryClass($this->class);
        }

        echo $this->repositoryClass . ' does not exists';
        die();
    }
}
