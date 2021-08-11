<?php

namespace Kunlabo\Shared\Application\Bus\Query;

interface QueryBus
{
    public function ask(Query $query): ?Response;
}