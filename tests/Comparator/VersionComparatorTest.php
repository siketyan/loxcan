<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Comparator;

use PHPUnit\Framework\TestCase;
use Siketyan\Loxcan\Versioning\Version;
use Siketyan\Loxcan\Versioning\VersionDiff;

class VersionComparatorTest extends TestCase
{
    private VersionComparator $comparator;

    protected function setUp(): void
    {
        $this->comparator = new VersionComparator();
    }

    public function test(): void
    {
        $this->assertCompare(
            VersionDiff::UPGRADED,
            new Version(1, 2, 3, 4),
            new Version(2, 0, 0, 0),
        );

        $this->assertCompare(
            VersionDiff::DOWNGRADED,
            new Version(4, 3, 2, 1),
            new Version(3, 4, 5, 6),
        );

        $this->assertCompare(
            null,
            new Version(1, 2, 3, null),
            new Version(1, 2, 3, null),
        );
    }

    private function assertCompare(?int $type, Version $before, Version $after): void
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
