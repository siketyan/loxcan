<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Versioning;

use JetBrains\PhpStorm\Pure;

interface CompatibilityAwareInterface
{
    /**
     * Returns a number to assume to have compatibility.
     * If two versions returns the save number via this method,
     * there is compatibility between them.
     *
     * In Semantic Versioning (SemVer), first number of their version
     * must be incremented after having a B.C. breaking changes generally.
     * Therefore, SemVer can return the first number as a compatibility number.
     *
     * @return int
     */
    #[Pure]
    public function getCompatibilityNumber(): int;
}
