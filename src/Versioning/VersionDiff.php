<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Versioning;

class VersionDiff
{
    public const UPGRADED = 1;
    public const DOWNGRADED = -1;
    public const CHANGED = 9;
    public const UNKNOWN = 0;

    public function __construct(
        private int $type,
        private VersionInterface $before,
        private VersionInterface $after
    ) {
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function getBefore(): VersionInterface
    {
        return $this->before;
    }

    public function getAfter(): VersionInterface
    {
        return $this->after;
    }
}
