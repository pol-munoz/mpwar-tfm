<?php

namespace Kunlabo\Action\Domain;

interface MessageService
{
    public function messageAction(Action $action): void;
}