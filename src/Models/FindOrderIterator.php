<?php declare(strict_types=1);

namespace VitesseCms\Database\Models;

use ArrayIterator;

class FindOrderIterator extends ArrayIterator
{
    public function __construct(array $findOrder = [])
    {
        parent::__construct($findOrder);
    }

    public function current(): FindOrder
    {
        return parent::current();
    }

    public function add(FindOrder $findOrder): FindOrderIterator
    {
        $this->append($findOrder);

        return $this;
    }
}
