<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Model;

class VersionDiff
{
    public const UPGRADED = 1;
    public const DOWNGRADED = -1;

    private int $type;
    private Version $before;
    private Version $after;

    public function __construct(
        int $type,
        Version $before,
        Version $after
    ) {
        $this->type = $type;
        $this->before = $before;
        $this->after = $after;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function getBefore(): Version
    {
        return $this->before;
    }

    public function getAfter(): Version
    {
        return $this->after;
    }
}
