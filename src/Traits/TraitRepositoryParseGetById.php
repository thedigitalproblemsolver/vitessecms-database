<?php

declare(strict_types=1);

namespace VitesseCms\Database\Traits;

use VitesseCms\Database\AbstractCollection;

trait TraitRepositoryParseGetById
{
    private function parseGetById(string $id, bool $hideUnpublished = true): ?AbstractCollection
    {
        $this->class::setFindPublished($hideUnpublished);

        /** @var AbstractCollection $collection */
        $collection = $this->class::findById($id);
        if (is_object($collection)):
            return $collection;
        endif;

        return null;
    }
}