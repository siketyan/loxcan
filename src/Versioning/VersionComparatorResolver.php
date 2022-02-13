<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Versioning;

class VersionComparatorResolver
{
    /**
     * @var VersionComparatorInterface[]
     */
    private array $comparators;

    /**
     * @param VersionComparatorInterface[] $comparators
     */
    public function __construct(
        array $comparators
    ) {
        $this->comparators = $comparators;
    }

    public function resolve(VersionInterface $before, VersionInterface $after): ?VersionComparatorInterface
    {
        $beforeType = $before::class;
        $afterType = $after::class;

        foreach ($this->comparators as $comparator) {
            if ($comparator->supports($beforeType, $afterType)) {
                return $comparator;
            }
        }

        return null;
    }
}
