<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Versioning\SemVer;

use PHPUnit\Framework\TestCase;
use Siketyan\Loxcan\Versioning\VersionDiff;

class SemVerVersionComparatorTest extends TestCase
{
    private SemVerVersionComparator $comparator;

    protected function setUp(): void
    {
        $this->comparator = new SemVerVersionComparator();
    }

    public function test(): void
    {
        $this->assertCompare(
            VersionDiff::UPGRADED,
            new SemVerVersion(1, 2, 3),
            new SemVerVersion(2, 0, 0),
        );

        $this->assertCompare(
            VersionDiff::DOWNGRADED,
            new SemVerVersion(1, 2, 3, ['beta', 1]),
            new SemVerVersion(1, 2, 3, ['beta']),
        );

        $this->assertCompare(
            VersionDiff::UPGRADED,
            new SemVerVersion(1, 2, 3, ['beta']),
            new SemVerVersion(1, 2, 3, ['rc']),
        );

        $this->assertCompare(
            VersionDiff::DOWNGRADED,
            new SemVerVersion(1, 2, 3),
            new SemVerVersion(1, 2, 3, ['alpha']),
        );

        $this->assertCompare(
            VersionDiff::UPGRADED,
            new SemVerVersion(1, 2, 3, ['alpha']),
            new SemVerVersion(1, 2, 3, ['beta']),
        );

        $this->assertCompare(
            VersionDiff::UPGRADED,
            new SemVerVersion(1, 2, 3, ['alpha', 12]),
            new SemVerVersion(1, 2, 3, ['alpha', 34]),
        );

        $this->assertCompare(
            null,
            new SemVerVersion(1, 2, 3, [], [123]),
            new SemVerVersion(1, 2, 3, [], [456]),
        );

        $this->assertCompare(
            null,
            new SemVerVersion(2, 3, 4),
            new SemVerVersion(2, 3, 4),
        );
    }

    private function assertCompare(?int $type, SemVerVersion $before, SemVerVersion $after): void
    {
        $diff = $this->comparator->compare($before, $after);

        if ($type === null) {
            $this->assertNull($diff);

            return;
        }

        $this->assertNotNull($diff);
        $this->assertSame($type, $diff->getType());
        $this->assertSame($before, $diff->getBefore());
        $this->assertSame($after, $diff->getAfter());
    }
}
