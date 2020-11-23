<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Comparator;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Siketyan\Loxcan\Model\Dependency;
use Siketyan\Loxcan\Model\DependencyCollection;
use Siketyan\Loxcan\Model\DependencyDiff;
use Siketyan\Loxcan\Model\Package;

class DependencyCollectionIntersectorTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy $comparator;
    private DependencyCollectionIntersector $intersector;

    protected function setUp(): void
    {
        $this->comparator = $this->prophesize(DependencyComparator::class);

        $this->intersector = new DependencyCollectionIntersector(
            $this->comparator->reveal(),
        );
    }

    public function test(): void
    {
        $package = $this->prophesize(Package::class)->reveal();
        $diff = $this->prophesize(DependencyDiff::class)->reveal();

        $before = $this->prophesize(Dependency::class);
        $after = $this->prophesize(Dependency::class);

        $before->getPackage()->willReturn($package);
        $after->getPackage()->willReturn($package);

        $this->comparator->compare($before->reveal(), $after->reveal())->willReturn($diff);

        $a = $this->prophesize(DependencyCollection::class);
        $b = $this->prophesize(DependencyCollection::class);

        $a->getDependencies()->willReturn([$before->reveal(), $this->createDummyDependency()]);
        $b->getDependencies()->willReturn([$after->reveal(), $this->createDummyDependency()]);

        $this->assertSame([$diff], $this->intersector->intersect($a->reveal(), $b->reveal()));
    }

    private function createDummyDependency(): Dependency
    {
        $package = $this->prophesize(Package::class)->reveal();
        $dependency = $this->prophesize(Dependency::class);

        $dependency
            ->getPackage()
            ->willReturn($package)
        ;

        return $dependency->reveal();
    }
}
