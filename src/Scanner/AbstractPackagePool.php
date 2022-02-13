<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Scanner;

use Siketyan\Loxcan\Model\Package;

abstract class AbstractPackagePool
{
    /**
     * @param Package[] $packages
     */
    public function __construct(
        private array $packages = []
    ) {
    }

    public function get(string $name): ?Package
    {
        return $this->packages[$name] ?? null;
    }

    public function add(Package $package): void
    {
        $this->packages[$package->getName()] = $package;
    }
}
