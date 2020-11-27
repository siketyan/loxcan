<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Model;

use Siketyan\Loxcan\Versioning\VersionInterface;

class Dependency
{
    private Package $package;
    private VersionInterface $version;

    public function __construct(
        Package $package,
        VersionInterface $version
    ) {
        $this->package = $package;
        $this->version = $version;
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
