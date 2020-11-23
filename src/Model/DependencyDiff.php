<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Model;

class DependencyDiff
{
    private Package $package;
    private VersionDiff $versionDiff;

    public function __construct(
        Package $package,
        VersionDiff $versionDiff
    ) {
        $this->package = $package;
        $this->versionDiff = $versionDiff;
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
