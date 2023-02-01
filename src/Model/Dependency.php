<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Model;

use Siketyan\Loxcan\Versioning\VersionInterface;

class Dependency
{
    public function __construct(
        private Package $package,
        private VersionInterface $version,
    ) {
    }

    public function getPackage(): Package
    {
        return $this->package;
    }

    public function getVersion(): VersionInterface
    {
        return $this->version;
    }
}
