<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Versioning;

interface CompatibilityAwareInterface
{
    public function isCompatibleWith(self $another): bool;
}
