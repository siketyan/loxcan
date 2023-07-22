<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Versioning\Simple;

use PHPUnit\Framework\TestCase;
use Siketyan\Loxcan\Versioning\VersionDiff;

class VersionComparatorTest extends TestCase
{
    private SimpleVersionComparator $comparator;

    protected function setUp(): void
    {
        $this->comparator = new SimpleVersionComparator();
    }

    public function test(): void
    {
        $this->assertCompare(
            VersionDiff::UPGRADED,
            new SimpleVersion(1, 2, 3, 4),
            new SimpleVersion(2, 0, 0, 0),
        );

        $this->assertCompare(
            VersionDiff::DOWNGRADED,
            new SimpleVersion(4, 3, 2, 1),
            new SimpleVersion(3, 4, 5, 6),
        );

        $this->assertCompare(
            null,
            new SimpleVersion(1, 2, 3, null),
            new SimpleVersion(1, 2, 3, null),
        );
    }

    private function assertCompare(?int $type, SimpleVersion $before, SimpleVersion $after): void
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
