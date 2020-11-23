<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Model;

class DependencyDiff
{
    private Package $package;
    private Version $before;
    private Version $after;

    public function __construct(
        Package $package,
        Version $before,
        Version $after
    ) {
        $this->package = $package;
        $this->before = $before;
        $this->after = $after;
    }

    public function getPackage(): Package
    {
        return $this->package;
    }

    public function getBefore(): Version
    {
        return $this->before;
    }

    public function getAfter(): Version
    {
        return $this->after;
    }
}
