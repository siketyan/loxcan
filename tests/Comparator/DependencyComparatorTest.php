<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Comparator;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Siketyan\Loxcan\Exception\InvalidComparisonException;
use Siketyan\Loxcan\Model\Dependency;
use Siketyan\Loxcan\Model\DependencyDiff;
use Siketyan\Loxcan\Model\Package;
use Siketyan\Loxcan\Model\Version;
use Siketyan\Loxcan\Model\VersionDiff;

class DependencyComparatorTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy $versionComparator;
    private DependencyComparator $comparator;

    protected function setUp(): void
    {
        $this->versionComparator = $this->prophesize(VersionComparator::class);

        $this->comparator = new DependencyComparator(
            $this->versionComparator->reveal(),
        );
    }

    public function test(): void
    {
        $package = $this->prophesize(Package::class)->reveal();
        $versionDiff = $this->prophesize(VersionDiff::class)->reveal();
        $beforeVersion = $this->prophesize(Version::class)->reveal();
        $afterVersion = $this->prophesize(Version::class)->reveal();
        $before = $this->prophesize(Dependency::class);
        $after = $this->prophesize(Dependency::class);

        $before->getPackage()->willReturn($package);
        $before->getVersion()->willReturn($beforeVersion);
        $after->getPackage()->willReturn($package);
        $after->getVersion()->willReturn($afterVersion);

        $this->versionComparator->compare($beforeVersion, $afterVersion)->willReturn($versionDiff);

        $diff = $this->comparator->compare($before->reveal(), $after->reveal());

        $this->assertInstanceOf(DependencyDiff::class, $diff);
        $this->assertSame($package, $diff->getPackage());
        $this->assertSame($versionDiff, $diff->getVersionDiff());
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
