<?php

namespace Kunlabo\Action\Infrastructure;

use Kunlabo\Action\Domain\Action;
use Kunlabo\Action\Domain\PersistService;
use Kunlabo\Shared\Domain\Utils;
use Kunlabo\Shared\Domain\ValueObject\Uuid;

final class FilePersistService implements PersistService
{
    const BASE_PATH = 'persisted/';

    public function persistAction(Action $action): void
    {
        $path = self::BASE_PATH;
        if (!file_exists($path)) {
            mkdir($path);
        }

        $path .= $action->getStudyId() . '/';
        if (!file_exists($path)) {
            mkdir($path);
        }

        $path .= $action->getParticipantId() . '.json';

        $data = [];

        if (file_exists($path)) {
            $contents = file_get_contents($path);
            $data = json_decode($contents, true);
        }

        $data = array_replace_recursive($data, $action->getBody());

        file_put_contents($path, json_encode($data));
    }

    public function deleteActionsForStudyId(Uuid $studyId)
    {
        $path = self::BASE_PATH;
        $path .= $studyId . '/';
        Utils::fullyDeleteDir($path);
    }
}