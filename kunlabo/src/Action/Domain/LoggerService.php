<?php

namespace Kunlabo\Action\Domain;

interface LoggerService
{
    public function logAction(Action $action): void;
}