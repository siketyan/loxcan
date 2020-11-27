<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Versioning;

class VersionDiff
{
    public const UPGRADED = 1;
    public const DOWNGRADED = -1;

    private int $type;
    private VersionInterface $before;
    private VersionInterface $after;

    public function __construct(
        int $type,
        VersionInterface $before,
        VersionInterface $after
    ) {
        $this->type = $type;
        $this->before = $before;
        $this->after = $after;
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
