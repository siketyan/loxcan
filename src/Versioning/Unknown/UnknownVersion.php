<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Versioning\Unknown;

use Siketyan\Loxcan\Versioning\VersionInterface;

class UnknownVersion implements VersionInterface
{
    private string $version;

    public function __construct(
        string $version
    ) {
        $this->version = $version;
    }

    public function __toString(): string
    {
        return $this->version;
    }
}
