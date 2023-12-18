<?php

declare(strict_types=1);

namespace VitesseCms\Database;

use DateTime;
use MongoDB\BSON\ObjectID;
use MongoDB\BSON\Regex;
use Phalcon\Http\Request;
use Phalcon\Incubator\MongoDB\Mvc\Collection;
use Phalcon\Incubator\MongoDB\Mvc\Collection\Behavior\SoftDelete;
use Phalcon\Incubator\MongoDB\Mvc\Collection\Behavior\Timestampable;
use Phalcon\Incubator\MongoDB\Mvc\CollectionInterface;
use Traversable;
use VitesseCms\Core\Traits\BaseObjectTrait;
use VitesseCms\Database\Enums\FindValueTypeEnum;
use VitesseCms\Database\Interfaces\BaseCollectionInterface;
use VitesseCms\Database\Utils\MongoUtil;

//TODO aparte admin AbstractCollection maken?
abstract class AbstractCollection
    extends Collection
    implements BaseCollectionInterface
{
    use BaseObjectTrait;

    public static array $findValue = [];
    protected static bool $findDeletedOn = true;
    protected static bool $findPublished = true;
    protected static array $findOrdering = [];
    protected static ?array $fields = [];
    protected static int $findLimit = 12;
    protected static bool $findParseFilter = false;
    public ?string $createdAt;
    public ?string $updatedOn = null;
    public ?string $deletedOn = null;
    public bool $published = false;
    protected ?string $adminListName = null;
    protected ?string $adminListExtra = null;

    public static function count(?array $parameters = null): int
    {
        $parameters[] = self::buildFindParameters();
        $number = parent::count($parameters);

        self::reset();

        return $number;
    }

    public static function buildFindParameters(): array
    {
        if (self::$findDeletedOn) :
            self::setFindValue('deletedOn', null);
        endif;

        if (self::$findPublished) :
            self::setFindValue('published', true);
        endif;

        return self::$findValue;
    }

    public static function setFindValue(string $key, $value, string $type = 'string'): void
    {
        self::$findValue[$key] = match ($type) {
            FindValueTypeEnum::LIKE->value => new Regex(preg_quote($value, '') . '(.)?', 'ig'),
            FindValueTypeEnum::NOT->value => ['$ne' => $value],
            FindValueTypeEnum::GREATER_THAN->value => ['$gt' => $value],
            FindValueTypeEnum::SMALLER_THAN->value => ['$lt' => $value],
            FindValueTypeEnum::BETWEEN->value => ['$gt' => $value[0], '$lt' => $value[1]],
            FindValueTypeEnum::IN_ARRAY->value => ['$in' => $value],
            default => $value,
        };
    }

    public static function reset()
    {
        self::$findValue = [];
        self::$findOrdering = [];
        self::$fields = null;
        self::setFindDeletedOn(true);
        self::setFindPublished(true);
        self::setFindParseFilter(false);
        self::$findLimit = 999;
    }

    public static function setFindDeletedOn(bool $state)
    {
        self::$findDeletedOn = $state;
    }

    public static function setFindPublished(bool $state)
    {
        self::$findPublished = $state;
    }

    public static function setFindParseFilter(bool $state)
    {
        self::$findParseFilter = $state;
    }

    public static function addFindOrder(string $key, int $direction = 1)
    {
        self::$findOrdering[$key] = $direction;
    }

    public static function removeFindOrder(string $key)
    {
        if (isset(self::$findOrdering[$key])) :
            unset(self::$findOrdering[$key]);
        endif;
    }

    public static function findAll(): array
    {
        if (self::$findParseFilter) :
            self::parseFilterInput();
        endif;

        $p = [
            self::buildFindParameters(),
            'sort' => self::$findOrdering,
        ];

        $p['limit'] = self::$findLimit;

        if (self::$fields !== null):
            $p['fields'] = self::$fields;
        endif;

        $result = parent::find($p);

        self::reset();

        return $result->toArray();
    }

    public static function parseFilterInput()
    {
        $request = new Request();
        $filter = $request->get('filter', null, []);
        $range = $fields = [];

        foreach ($filter as $key => $value) :
            if (is_string($value)) :
                $value = trim($value);
            endif;

            if (!empty($value)) :
                if ($key === 'range' && is_array($value)) :
                    $keys = array_keys($value);
                    $key = $keys[0];
                    $range = explode(',', $value[$keys[0]]);
                    $value = 'range';
                endif;
                if ($key === 'textFields') :
                    $fields = $value;
                    $value = $key;
                endif;

                if ($key === 'datagroup' && isset(self::$findValue['datagroup'])) :
                    $value = 'disabled';
                endif;

                switch ($value):
                    case 'false':
                        self::setFindValue($key, false);
                        break;
                    case 'range':
                        self::setFindValue($key, [
                            '$gte' => (int)$range[0],
                            '$lte' => (int)$range[1],
                        ]);
                        break;
                    case 'textFields':
                        if (is_array($fields) && $request->get('search')) :
                            foreach ($fields as $fieldName => $fieldValue) :
                                self::setFindValue($fieldName, $request->get('search'), 'like');
                            endforeach;
                        endif;
                        break;
                    case 'true':
                        self::setFindValue($key, true);
                        break;
                    case 'disabled':
                        break;
                    default:
                        if (is_array($value)) :
                            self::setFindValue($key, ['$in' => $value]);
                        elseif (is_numeric($value)) :
                            self::setFindValue($key, ['$in' => [(int)$value, $value]]);
                        elseif (MongoUtil::isObjectId($value)):
                            self::setFindValue($key, $value);
                        else :
                            self::setFindValue($key, $value, 'like');
                        endif;
                endswitch;
            endif;
        endforeach;
    }

    public static function find(array $parameters = []): Traversable
    {
        $params = [
            'deletedOn' => null,
            'published' => true,
        ];

        if (isset($parameters['limit'])) :
            $params['limit'] = $parameters['limit'];

        endif;
        $item = parent::find([$params]);

        self::reset();

        return $item;
    }

    public function toArray(): array
    {
        $return = parent::toArray();
        unset(
            $return['_dependencyInjector'],
            $return['_modelsManager'],
            $return['_source'],
            $return['_operationMade'],
            $return['_dirtyState'],
            $return['_connection'],
            $return['_errorMessages'],
            $return['_skipped'],
            $return['di']
        );

        return $return;
    }

    public static function setFindLimit(int $limit): void
    {
        self::$findLimit = $limit;
    }

    public static function setReturnFields(array $fields): void
    {
        self::$fields = $fields;
    }

    public static function setRenderFields(bool $value)
    {
    }

    public static function findById($id): ?CollectionInterface
    {
        if (empty($id) || !MongoUtil::isObjectId((string)$id)) :
            return null;
        endif;

        if (!is_object($id)) {
            $mongoId = new ObjectID($id);
        } else {
            $mongoId = $id;
        }

        self::setFindValue('_id', $mongoId);

        return self::findFirst();
    }

    public static function findFirst(array $parameters = []): ?CollectionInterface
    {
        $parameters[] = self::buildFindParameters();
        if (self::$findOrdering) :
            $parameters['sort'] = self::$findOrdering;
        endif;

        $item = parent::findFirst(
            $parameters
        );

        self::reset();

        return $item;
    }

    public function onConstruct()
    {
    }

    /**
     * @deprecated removed from Models
     */
    public function afterFetch()
    {
    }

    public function initialize()
    {
        $this->addBehavior(
            new SoftDelete([
                'field' => 'deletedOn',
                'value' => date('Y-m-d H:i:s'),
            ])
        );

        $this->addBehavior(
            new Timestampable(
                [
                    'beforeCreate' => [
                        'field' => 'createdAt',
                        'format' => 'Y-m-d H:i:s',
                    ],
                    'beforeUpdate' => [
                        'field' => 'updatedOn',
                        'format' => 'Y-m-d H:i:s',
                    ],
                ]
            )
        );

        if ($this->published === null) :
            $this->published = false;
        endif;
    }

    public function getAdminlistName(): string
    {
        if ($this->adminListName !== null):
            return $this->adminListName;
        endif;

        return $this->getNameField();
    }

    public function setAdminListName(string $adminListName): self
    {
        $this->adminListName = $adminListName;

        return $this;
    }

    //TODO add listener

    public function save(): bool
    {
        if ($this->getId() === null) :
            $this->setId(new ObjectID());
        endif;

        try {
            $result = parent::save();
            $this->afterSave();

            return $result;
        } catch (Exception $exception) {
            throw new Exception('Saving of Item failed : ' . $exception->getMessage());
        }
    }

    public function afterSave()
    {
    }

    public function resetId(): void
    {
        $this->_id = null;
    }

    public function beforeSave(): void
    {
        $this->collectionsManager->notifyEvent(get_class($this) . ':beforeSave', $this);
    }

    public function beforeDelete(): ?bool
    {
        return $this->getDI()->get('eventsManager')->fire(get_class($this) . ':beforeDelete', $this);
    }

    public function afterDelete()
    {
    }

    public function beforePublish()
    {
    }

    public function afterPublish()
    {
    }

    public function getAdminButtons(): string
    {
        return '';
    }

    public function getCreateDate(): DateTime
    {
        return new DateTime($this->createdAt ?? '');
    }

    public function getUpdatedOn(): DateTime
    {
        if ($this->updatedOn !== null) :
            return DateTime::createFromFormat('Y-m-d H:i:s', $this->updatedOn);
        endif;

        return new DateTime($this->createdAt ?? '');
    }

    public function isPublished(): bool
    {
        return (bool)$this->published;
    }

    public function setPublished(bool $published): BaseCollectionInterface
    {
        $this->published = $published;

        return $this;
    }

    public function isDeleted(): bool
    {
        return $this->deletedOn !== null;
    }

    public function getAdminListExtra(): string
    {
        return $this->adminListExtra ?? '';
    }

    public function setAdminListExtra(string $adminListExtra): AbstractCollection
    {
        $this->adminListExtra = $adminListExtra;

        return $this;
    }
}
