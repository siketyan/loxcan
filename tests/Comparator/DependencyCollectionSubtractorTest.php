<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Comparator;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Siketyan\Loxcan\Model\Dependency;
use Siketyan\Loxcan\Model\DependencyCollection;
use Siketyan\Loxcan\Model\Package;

class DependencyCollectionSubtractorTest extends TestCase
{
    use ProphecyTrait;

    private DependencyCollectionSubtractor $subtractor;

    protected function setUp(): void
    {
        $this->subtractor = new DependencyCollectionSubtractor();
    }

    public function test(): void
    {
        $added = $this->prophesize(Dependency::class);
        $removed = $this->prophesize(Dependency::class);
        $common = $this->prophesize(Dependency::class);

        $added->getPackage()->willReturn($this->prophesize(Package::class)->reveal());
        $removed->getPackage()->willReturn($this->prophesize(Package::class)->reveal());
        $common->getPackage()->willReturn($this->prophesize(Package::class)->reveal());

        $a = $this->prophesize(DependencyCollection::class);
        $b = $this->prophesize(DependencyCollection::class);

        $a->getDependencies()->willReturn([$common->reveal(), $removed->reveal()]);
        $b->getDependencies()->willReturn([$common->reveal(), $added->reveal()]);

        $this->assertSame([$added->reveal()], $this->subtractor->subtract($b->reveal(), $a->reveal()));
        $this->assertSame([$removed->reveal()], $this->subtractor->subtract($a->reveal(), $b->reveal()));
    }
}
