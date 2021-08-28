<?php

namespace Kunlabo\Action\Domain;

interface PersistService
{
    public function persistAction(Action $action): void;
}