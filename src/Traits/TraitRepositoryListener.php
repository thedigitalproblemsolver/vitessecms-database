<?php

declare(strict_types=1);

namespace VitesseCms\Database\Traits;

trait TraitRepositoryListener
{
    private readonly string $repositoryClass;

    private function setRepositoryClass(string $class): void
    {
        $this->repositoryClass = str_replace(['Models'], ['Repositories'], $class . 'Repository');
    }

    private function parseGetRepository()
    {
        if (class_exists($this->repositoryClass)) {
            return new $this->repositoryClass($this->class);
        }

        echo $this->repositoryClass . ' does not exists';
        die();
    }
}
