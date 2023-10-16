<?php

declare(strict_types=1);

namespace VitesseCms\Database\Traits;

use VitesseCms\Database\Models\FindOrderIterator;
use VitesseCms\Database\Models\FindValueIterator;

trait TraitRepositoryParseFindAll
{
    use TraitRepositoryParseFindValues;
    use TraitRepositoryParseFindOrders;

    private function parseFindAll(
        ?FindValueIterator $findValues = null,
        bool $hideUnpublished = true,
        ?int $limit = null,
        ?FindOrderIterator $findOrders = null,
        ?array $returnFields = null
    ): \ArrayIterator {
        $this->class::setFindPublished($hideUnpublished);

        if ($limit !== null) {
            $this->class::setFindLimit($limit);
        }

        if ($returnFields !== null) {
            $this->class::setReturnFields($returnFields);
        }

        $this->parsefindValues($findValues);
        $this->parseFindOrders($findOrders);

        return new $this->classIterator($this->class::findAll());
    }
}