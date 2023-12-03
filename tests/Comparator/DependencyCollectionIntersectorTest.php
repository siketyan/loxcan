<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Comparator;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Siketyan\Loxcan\Model\Dependency;
use Siketyan\Loxcan\Model\DependencyCollection;
use Siketyan\Loxcan\Model\DependencyDiff;
use Siketyan\Loxcan\Model\Package;

class DependencyCollectionIntersectorTest extends TestCase
{
    private DependencyComparator&MockObject $comparator;
    private DependencyCollectionIntersector $intersector;

    protected function setUp(): void
    {
        $this->comparator = $this->createMock(DependencyComparator::class);
        $this->intersector = new DependencyCollectionIntersector($this->comparator);
    }

    public function test(): void
    {
        $package = $this->createStub(Package::class);
        $diff = $this->createStub(DependencyDiff::class);

        $before = $this->createStub(Dependency::class);
        $after = $this->createStub(Dependency::class);

        $before->method('getPackage')->willReturn($package);
        $after->method('getPackage')->willReturn($package);

        $this->comparator->method('compare')->with($before, $after)->willReturn($diff);

        $a = $this->createStub(DependencyCollection::class);
        $b = $this->createStub(DependencyCollection::class);

        $a->method('getDependencies')->willReturn([$before, $this->createDummyDependency()]);
        $b->method('getDependencies')->willReturn([$after, $this->createDummyDependency()]);

        $this->assertSame([$diff], $this->intersector->intersect($a, $b));
    }

    private function createDummyDependency(): Dependency&Stub
    {
        $package = $this->createStub(Package::class);
        $dependency = $this->createStub(Dependency::class);

        $dependency
            ->method('getPackage')
            ->willReturn($package)
        ;

        return $dependency;
    }
}
