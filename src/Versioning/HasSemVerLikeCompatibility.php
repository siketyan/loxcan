<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Versioning;

use JetBrains\PhpStorm\Pure;

/**
 * @implements CompatibilityAwareInterface
 */
trait HasSemVerLikeCompatibility
{
    /**
     * {@inheritDoc}
     */
    #[Pure]
    public function getCompatibilityNumber(): int
    {
        // For 0.Y.* versions, uses Y as a compatibility number instead of X in X.*.* versions.
        if ($this->getX() === 0) {
            // To avoid returning the same number from 0.Y.* and X.*.*,
            // inverts the number to minus here assuming both X and Y are positive.
            return -$this->getY();
        }

        return $this->getX() * 10;
    }
}
