<?php

namespace Kunlabo\Action\Domain;

interface LogService
{
    public function logAction(Action $action): void;
}