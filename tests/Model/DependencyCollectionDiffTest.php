<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Model;

use PHPUnit\Framework\TestCase;

class DependencyCollectionDiffTest extends TestCase
{
    public function testCount(): void
    {
        $diff = new DependencyCollectionDiff();
        $this->assertSame(0, $diff->count());

        $dummy = $this->createStub(Dependency::class);
        $dummyDiff = $this->createStub(DependencyDiff::class);
        $diff = new DependencyCollectionDiff(
            [$dummy],
            [$dummyDiff, $dummyDiff],
            [$dummy, $dummy, $dummy],
        );

        $this->assertSame(6, $diff->count());
    }
}
