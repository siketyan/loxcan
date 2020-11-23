<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Comparator;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Siketyan\Loxcan\Exception\InvalidComparisonException;
use Siketyan\Loxcan\Model\Dependency;
use Siketyan\Loxcan\Model\DependencyDiff;
use Siketyan\Loxcan\Model\Package;
use Siketyan\Loxcan\Model\Version;

class DependencyComparatorTest extends TestCase
{
    use ProphecyTrait;

    private DependencyComparator $comparator;

    protected function setUp(): void
    {
        $this->comparator = new DependencyComparator();
    }

    public function test(): void
    {
        $package = $this->prophesize(Package::class)->reveal();
        $beforeVersion = $this->prophesize(Version::class)->reveal();
        $afterVersion = $this->prophesize(Version::class)->reveal();
        $before = $this->prophesize(Dependency::class);
        $after = $this->prophesize(Dependency::class);

        $before->getPackage()->willReturn($package);
        $before->getVersion()->willReturn($beforeVersion);
        $after->getPackage()->willReturn($package);
        $after->getVersion()->willReturn($afterVersion);

        $diff = $this->comparator->compare($before->reveal(), $after->reveal());

        $this->assertInstanceOf(DependencyDiff::class, $diff);
        $this->assertSame($package, $diff->getPackage());
        $this->assertSame($beforeVersion, $diff->getBefore());
        $this->assertSame($afterVersion, $diff->getAfter());
    }

    public function testInvalidComparison(): void
    {
        $this->expectException(InvalidComparisonException::class);

        $beforePackage = $this->prophesize(Package::class)->reveal();
        $afterPackage = $this->prophesize(Package::class)->reveal();
        $before = $this->prophesize(Dependency::class);
        $after = $this->prophesize(Dependency::class);

        $before->getPackage()->willReturn($beforePackage);
        $after->getPackage()->willReturn($afterPackage);

        $this->comparator->compare($before->reveal(), $after->reveal());
    }
}
