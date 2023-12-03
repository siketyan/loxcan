<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Comparator;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Siketyan\Loxcan\Model\Dependency;
use Siketyan\Loxcan\Model\DependencyCollection;
use Siketyan\Loxcan\Model\DependencyCollectionDiff;
use Siketyan\Loxcan\Model\DependencyDiff;

class DependencyCollectionComparatorTest extends TestCase
{
    private DependencyCollectionSubtractor&MockObject $subtractor;
    private DependencyCollectionIntersector&MockObject $intersector;
    private DependencyCollectionComparator $comparator;

    protected function setUp(): void
    {
        $this->subtractor = $this->createMock(DependencyCollectionSubtractor::class);
        $this->intersector = $this->createMock(DependencyCollectionIntersector::class);
        $this->comparator = new DependencyCollectionComparator(
            $this->subtractor,
            $this->intersector,
        );
    }

    public function test(): void
    {
        $added = [$this->createStub(Dependency::class)];
        $updated = [$this->createStub(DependencyDiff::class)];
        $removed = [$this->createStub(Dependency::class)];

        $before = $this->createStub(DependencyCollection::class);
        $after = $this->createStub(DependencyCollection::class);

        $this->subtractor
            ->expects($this->exactly(2))
            ->method('subtract')
            ->with($this->isInstanceOf(DependencyCollection::class), $this->isInstanceOf(DependencyCollection::class))
            ->willReturnCallback(
                fn (DependencyCollection $a, DependencyCollection $b): array => ($a === $before && $b === $after) ? $removed : $added,
            )
        ;

        $this->intersector
            ->method('intersect')
            ->with($this->identicalTo($before), $this->identicalTo($after))
            ->willReturn($updated)
        ;

        $diff = $this->comparator->compare($before, $after);

        $this->assertInstanceOf(DependencyCollectionDiff::class, $diff);
        $this->assertSame($added, $diff->getAdded());
        $this->assertSame($updated, $diff->getUpdated());
        $this->assertSame($removed, $diff->getRemoved());
    }
}
