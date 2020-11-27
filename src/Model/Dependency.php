<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Model;

use Siketyan\Loxcan\Versioning\Version;

class Dependency
{
    private Package $package;
    private Version $version;

    public function __construct(
        Package $package,
        Version $version
    ) {
        $this->package = $package;
        $this->version = $version;
    }

    public function getPackage(): Package
    {
        return $this->package;
    }

    public function getVersion(): Version
    {
        return $this->version;
    }
}
