<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Versioning\Composer;

use PHPUnit\Framework\TestCase;
use Siketyan\Loxcan\Versioning\VersionDiff;

class ComposerVersionComparatorTest extends TestCase
{
    private ComposerVersionComparator $comparator;

    protected function setUp(): void
    {
        $this->comparator = new ComposerVersionComparator();
    }

    public function test(): void
    {
        $this->assertCompare(
            VersionDiff::UPGRADED,
            new ComposerVersion(1, 2, 3),
            new ComposerVersion(2, 0, 0),
        );

        $this->assertCompare(
            VersionDiff::DOWNGRADED,
            new ComposerVersion(4, 3, 2, ComposerVersion::STABILITY_DEV),
            new ComposerVersion(3, 4, 5, ComposerVersion::STABILITY_DEV),
        );

        $this->assertCompare(
            VersionDiff::UPGRADED,
            new ComposerVersion(1, 2, 3, ComposerVersion::STABILITY_BETA),
            new ComposerVersion(1, 2, 3, ComposerVersion::STABILITY_RC),
        );

        $this->assertCompare(
            VersionDiff::CHANGED,
            new ComposerVersion(1, 2, 3, ComposerVersion::STABILITY_STABLE, 0, 'hash'),
            new ComposerVersion(1, 2, 3, ComposerVersion::STABILITY_STABLE, 0, 'hash_changed'),
        );

        $this->assertCompare(
            null,
            new ComposerVersion(2, 3, 4),
            new ComposerVersion(2, 3, 4),
        );
    }

    private function assertCompare(?int $type, ComposerVersion $before, ComposerVersion $after): void
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
