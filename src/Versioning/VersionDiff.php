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
        private int $type,
        private VersionInterface $before,
        private VersionInterface $after
    ) {
    }

    /**
     * Determine if any breaking changes are occurred between the two versions.
     * Refer CompatibilityAwareInterface::getCompatibilityNumber for how to determine that.
     *
     * @return bool true if there are breaking changes
     */
    #[Pure]
    public function hasBreakingChanges(): bool
    {
        if (!(($before = $this->getBefore()) instanceof CompatibilityAwareInterface) ||
            !(($after = $this->getAfter()) instanceof CompatibilityAwareInterface)) {
            return false;
        }

        /**
         * @var CompatibilityAwareInterface $before
         * @var CompatibilityAwareInterface $after
         */
        return $before->getCompatibilityNumber() !== $after->getCompatibilityNumber();
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
