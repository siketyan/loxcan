<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Model;

class DependencyCollectionDiff
{
    /**
     * @var Dependency[]
     */
    private array $added;

    /**
     * @var DependencyDiff[]
     */
    private array $updated;

    /**
     * @var Dependency[]
     */
    private array $removed;

    /**
     * @param Dependency[]     $added
     * @param DependencyDiff[] $updated
     * @param Dependency[]     $removed
     */
    public function __construct(
        array $added = [],
        array $updated = [],
        array $removed = []
    ) {
        $this->added = $added;
        $this->updated = $updated;
        $this->removed = $removed;
    }

    /**
     * @return Dependency[]
     */
    public function getAdded(): array
    {
        return $this->added;
    }

    /**
     * @return DependencyDiff[]
     */
    public function getUpdated(): array
    {
        return $this->updated;
    }

    /**
     * @return Dependency[]
     */
    public function getRemoved(): array
    {
        return $this->removed;
    }

    public function count(): int
    {
        return count($this->added) + count($this->updated) + count($this->removed);
    }
}
