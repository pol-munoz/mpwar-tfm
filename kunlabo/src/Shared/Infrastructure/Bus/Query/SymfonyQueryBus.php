<?php

namespace Kunlabo\Shared\Infrastructure\Bus\Query;

use Kunlabo\Shared\Application\Bus\Query\Query;
use Kunlabo\Shared\Application\Bus\Query\Response;
use Symfony\Component\Messenger\Exception\NoHandlerForMessageException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class SymfonyQueryBus
{
    public function __construct(private MessageBusInterface $queryBus)
    {
    }

    public function ask(Query $query): ?Response
    {
        try {
            /** @var HandledStamp $stamp */
            $stamp = $this->queryBus->dispatch($query)->last(HandledStamp::class);

            return $stamp->getResult();
        } catch (NoHandlerForMessageException) {
            throw new UnknownQueryException($query);
        }
    }
}