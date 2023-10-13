<?php

declare(strict_types=1);

namespace VitesseCms\Database\Traits;

use VitesseCms\Database\Models\FindOrderIterator;

trait TraitRepositoryParseFindOrders
{
    private function parseFindOrders(?FindOrderIterator $findOrders = null): void
    {
        if ($findOrders !== null) {
            while ($findOrders->valid()) {
                $findOrder = $findOrders->current();
                $this->class::addFindOrder(
                    $findOrder->getKey(),
                    $findOrder->getOrder()
                );
                $findOrders->next();
            }
        } else {
            $this->class::addFindOrder('name');
        }
    }
}