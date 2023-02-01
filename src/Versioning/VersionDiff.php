<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Versioning;

use JetBrains\PhpStorm\Pure;

class VersionDiff
{
    public const UPGRADED = 1;
    public const DOWNGRADED = -1;
    public const CHANGED = 9;
    public const UNKNOWN = 0;

    public function __construct(
        private readonly int $type,
        private readonly VersionInterface $before,
        private readonly VersionInterface $after,
    ) {
    }

    /**
     * Determine if two versions are compatible.
     *
     * @return bool true if there are compatible
     */
    #[Pure]
    public function isCompatible(): bool
    {
        if (!(($before = $this->getBefore()) instanceof CompatibilityAwareInterface)
            || !(($after = $this->getAfter()) instanceof CompatibilityAwareInterface)) {
            return false;
        }

        /* @var CompatibilityAwareInterface $before
         * @var CompatibilityAwareInterface $after */
        return $before->isCompatibleWith($after);
    }

    public function getType(): int
    {
        return $this->type;
    }

    #[Pure]
    public function getBefore(): VersionInterface
    {
        return $this->before;
    }

    #[Pure]
    public function getAfter(): VersionInterface
    {
        return $this->after;
    }
}
