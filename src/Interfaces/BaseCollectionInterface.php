<?php declare(strict_types=1);

namespace VitesseCms\Database\Interfaces;

use Phalcon\Incubator\MongoDB\Mvc\CollectionInterface;
use VitesseCms\Core\Interfaces\BaseObjectInterface;

interface BaseCollectionInterface extends BaseObjectInterface, CollectionInterface
{
    public function setPublished(bool $published): BaseCollectionInterface;

    public function isPublished(): bool;
}
