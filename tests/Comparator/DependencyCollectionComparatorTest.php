<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Comparator;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Siketyan\Loxcan\Model\Dependency;
use Siketyan\Loxcan\Model\DependencyCollection;
use Siketyan\Loxcan\Model\DependencyCollectionDiff;
use Siketyan\Loxcan\Model\DependencyDiff;

class DependencyCollectionComparatorTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var ObjectProphecy<DependencyCollectionSubtractor>
     */
    private ObjectProphecy $subtractor;

    /**
     * @var ObjectProphecy<DependencyCollectionIntersector>
     */
    private ObjectProphecy $intersector;

    private DependencyCollectionComparator $comparator;

    protected function setUp(): void
    {
        $this->subtractor = $this->prophesize(DependencyCollectionSubtractor::class);
        $this->intersector = $this->prophesize(DependencyCollectionIntersector::class);

        $this->comparator = new DependencyCollectionComparator(
            $this->subtractor->reveal(),
            $this->intersector->reveal(),
        );
    }

    public function test(): void
    {
        $added = [$this->prophesize(Dependency::class)->reveal()];
        $updated = [$this->prophesize(DependencyDiff::class)->reveal()];
        $removed = [$this->prophesize(Dependency::class)->reveal()];

        $before = $this->prophesize(DependencyCollection::class)->reveal();
        $after = $this->prophesize(DependencyCollection::class)->reveal();

        $this->subtractor->subtract($after, $before)->willReturn($added);
        $this->subtractor->subtract($before, $after)->willReturn($removed);
        $this->intersector->intersect($before, $after)->willReturn($updated);

        $diff = $this->comparator->compare($before, $after);

        $this->assertInstanceOf(DependencyCollectionDiff::class, $diff);
        $this->assertSame($added, $diff->getAdded());
        $this->assertSame($updated, $diff->getUpdated());
        $this->assertSame($removed, $diff->getRemoved());
    }
}
