<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Versioning;

interface CompatibilityAwareInterface extends SemVerLikeInterface
{
    public function isCompatibleWith(SemVerLikeInterface $another): bool;
}
