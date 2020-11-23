<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Model;

class FileDiff
{
    private string $before;
    private string $after;

    public function __construct(
        string $before,
        string $after
    ) {
        $this->before = $before;
        $this->after = $after;
    }

    public function getBefore(): string
    {
        return $this->before;
    }

    public function getAfter(): string
    {
        return $this->after;
    }
}
