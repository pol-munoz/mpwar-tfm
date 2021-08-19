<?php

namespace Kunlabo\Study\Application\Query;

use Kunlabo\Shared\Application\Bus\Query\Response;

final class StudiesResponse implements Response
{
    public function __construct(private array $studies)
    {
    }

    public function getStudies(): array
    {
        return $this->studies;
    }
}