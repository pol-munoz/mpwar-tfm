<?php

namespace Kunlabo\Shared\Application\Bus\Command;

interface CommandBus
{
    public function dispatch(Command $command): void;
}