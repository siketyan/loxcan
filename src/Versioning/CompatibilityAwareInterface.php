<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Versioning;

use JetBrains\PhpStorm\Pure;

interface CompatibilityAwareInterface
{
    #[Pure]
    public function isCompatibleWith(self $another): bool;
}
