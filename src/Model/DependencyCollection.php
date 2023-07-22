<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Model;

class DependencyCollection
{
    /**
     * @param list<Dependency> $dependencies
     */
    public function __construct(
        private readonly array $dependencies,
    ) {
    }

    /**
     * @return list<Dependency>
     */
    public function getDependencies(): array
    {
        return $this->dependencies;
    }
}
