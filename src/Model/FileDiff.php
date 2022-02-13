<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Model;

class FileDiff
{
    public function __construct(
        private ?string $before,
        private ?string $after
    ) {
    }

    public function getBefore(): ?string
    {
        return $this->before;
    }

    public function getAfter(): ?string
    {
        return $this->after;
    }
}
