<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Versioning;

use JetBrains\PhpStorm\Pure;

/**
 * @implements CompatibilityAwareInterface
 */
trait HasSemVerLikeCompatibility
{
    #[Pure]
    public function isCompatibleWith(CompatibilityAwareInterface $another): bool
    {
        // Let the version has a format of X.Y.Z.
        return $this::class === $another::class  // Two different version systems are not compatible.
            && $this->getX() === $another->getX()  // Basically, two version that have the same X are compatible.
            && ($this->getX() !== 0 || $this->getY() === $another->getY()) // For 0.Y.Z versions, the same Y are also needed.
        ;
    }
}
