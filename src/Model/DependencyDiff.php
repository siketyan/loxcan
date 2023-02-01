<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Model;

use Siketyan\Loxcan\Versioning\VersionDiff;

class DependencyDiff
{
    public function __construct(
        private Package $package,
        private VersionDiff $versionDiff,
    ) {
    }

    public function getPackage(): Package
    {
        return $this->package;
    }

    public function getVersionDiff(): VersionDiff
    {
        return $this->versionDiff;
    }
}
