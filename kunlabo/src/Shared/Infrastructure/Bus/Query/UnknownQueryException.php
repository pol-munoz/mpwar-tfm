<?php

namespace Kunlabo\Shared\Infrastructure\Bus\Query;

use Kunlabo\Shared\Application\Bus\Query\Query;
use RuntimeException;

final class UnknownQueryException extends RuntimeException
{
    public function __construct(Query $query)
    {
        $queryClass = get_class($query);

        parent::__construct("Unknown query (no handler): " . $queryClass);
    }
}