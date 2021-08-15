<?php


namespace Kunlabo\Shared\Domain\Aggregate;

use DateTime;
use Kunlabo\Shared\Domain\ValueObject\Name;
use Kunlabo\Shared\Domain\ValueObject\Uuid;

abstract class NamedAggregateRoot extends AggregateRoot
{
    protected function __construct(Uuid $id, DateTime $created, DateTime $modified, protected Name $name)
    {
        parent::__construct($id, $created, $modified);
    }

    public function getName(): Name
    {
        return $this->name;
    }
}