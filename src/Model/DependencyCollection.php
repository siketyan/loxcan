<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Model;

class DependencyCollection
{
    /**
     * @param Dependency[] $dependencies
     */
    public function __construct(
        private array $dependencies,
    ) {
    }

    /**
     * @return Dependency[]
     */
    public function getDependencies(): array
    {
        return $this->dependencies;
    }
}
