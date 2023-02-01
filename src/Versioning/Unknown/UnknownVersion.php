<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Versioning\Unknown;

use Siketyan\Loxcan\Versioning\VersionInterface;

class UnknownVersion implements VersionInterface, \Stringable
{
    public function __construct(
        private readonly string $version,
    ) {
    }

    public function __toString(): string
    {
        return $this->version;
    }
}
