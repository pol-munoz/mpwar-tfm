<?php

namespace Kunlabo\Study\Application\Query;

use Kunlabo\Shared\Application\Bus\Query\Response;
use Kunlabo\Study\Domain\Study;

final class StudyResponse implements Response
{
    public function __construct(private ?Study $study)
    {
    }

    public function getStudy(): ?Study
    {
        return $this->study;
    }
}