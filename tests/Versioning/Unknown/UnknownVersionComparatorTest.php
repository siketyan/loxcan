<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Versioning\Unknown;

use PHPUnit\Framework\TestCase;
use Siketyan\Loxcan\Versioning\SemVer\SemVerVersion;
use Siketyan\Loxcan\Versioning\Simple\SimpleVersion;
use Siketyan\Loxcan\Versioning\VersionDiff;
use Siketyan\Loxcan\Versioning\VersionInterface;

class UnknownVersionComparatorTest extends TestCase
{
    private UnknownVersionComparator $comparator;

    protected function setUp(): void
    {
        $this->comparator = new UnknownVersionComparator();
    }

    public function test(): void
    {
        $this->assertCompare(
            VersionDiff::UNKNOWN,
            new UnknownVersion('1.2.3.4'),
            new SimpleVersion(1, 2, 3, 4),
        );

        $this->assertCompare(
            VersionDiff::UNKNOWN,
            new UnknownVersion('1.2.3.4'),
            new UnknownVersion('2.3.4.5'),
        );

        $this->assertCompare(
            VersionDiff::UNKNOWN,
            new SimpleVersion(1, 2, 3, 4),
            new SimpleVersion(2, 3, 4, 5),
        );
    }

    public function testSupports(): void
    {
        $this->assertTrue(
            $this->comparator->supports(UnknownVersion::class, UnknownVersion::class),
        );

        $this->assertTrue(
            $this->comparator->supports(SimpleVersion::class, UnknownVersion::class),
        );

        $this->assertTrue(
            $this->comparator->supports(SemVerVersion::class, SimpleVersion::class),
        );

        $this->assertFalse(
            $this->comparator->supports(UnknownVersion::class, VersionDiff::class),
        );
    }

    private function assertCompare(?int $type, VersionInterface $before, VersionInterface $after): void
    {
        $diff = $this->comparator->compare($before, $after);

        if ($type === null) {
            $this->assertNull($diff);

            return;
        }

        $this->assertSame($type, $diff->getType());
        $this->assertSame($before, $diff->getBefore());
        $this->assertSame($after, $diff->getAfter());
    }
}
