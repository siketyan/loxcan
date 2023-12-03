<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Comparator;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Siketyan\Loxcan\Exception\InvalidComparisonException;
use Siketyan\Loxcan\Model\Dependency;
use Siketyan\Loxcan\Model\DependencyDiff;
use Siketyan\Loxcan\Model\Package;
use Siketyan\Loxcan\Versioning\Simple\SimpleVersion;
use Siketyan\Loxcan\Versioning\VersionComparatorInterface;
use Siketyan\Loxcan\Versioning\VersionComparatorResolver;
use Siketyan\Loxcan\Versioning\VersionDiff;

class DependencyComparatorTest extends TestCase
{
    private MockObject&VersionComparatorResolver $versionComparatorResolver;
    private DependencyComparator $comparator;

    protected function setUp(): void
    {
        $this->versionComparatorResolver = $this->createMock(VersionComparatorResolver::class);
        $this->comparator = new DependencyComparator($this->versionComparatorResolver);
    }

    public function test(): void
    {
        $package = $this->createStub(Package::class);
        $versionDiff = $this->createStub(VersionDiff::class);
        $beforeVersion = $this->createStub(SimpleVersion::class);
        $afterVersion = $this->createStub(SimpleVersion::class);
        $before = $this->createStub(Dependency::class);
        $after = $this->createStub(Dependency::class);

        $before->method('getPackage')->willReturn($package);
        $before->method('getVersion')->willReturn($beforeVersion);
        $after->method('getPackage')->willReturn($package);
        $after->method('getVersion')->willReturn($afterVersion);

        $versionComparator = $this->createMock(VersionComparatorInterface::class);
        $versionComparator->method('compare')->with($beforeVersion, $afterVersion)->willReturn($versionDiff);

        $this->versionComparatorResolver
            ->method('resolve')
            ->with($beforeVersion, $afterVersion)
            ->willReturn($versionComparator)
        ;

        $diff = $this->comparator->compare($before, $after);

        $this->assertInstanceOf(DependencyDiff::class, $diff);
        $this->assertSame($package, $diff->getPackage());
        $this->assertSame($versionDiff, $diff->getVersionDiff());
    }

    public function testInvalidComparison(): void
    {
        $this->expectException(InvalidComparisonException::class);

        $beforePackage = $this->createStub(Package::class);
        $afterPackage = $this->createStub(Package::class);
        $before = $this->createStub(Dependency::class);
        $after = $this->createStub(Dependency::class);

        $before->method('getPackage')->willReturn($beforePackage);
        $after->method('getPackage')->willReturn($afterPackage);

        $this->comparator->compare($before, $after);
    }
}
