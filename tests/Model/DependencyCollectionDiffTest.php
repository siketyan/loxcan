<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Model;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class DependencyCollectionDiffTest extends TestCase
{
    use ProphecyTrait;

    public function testCount(): void
    {
        $diff = new DependencyCollectionDiff();
        $this->assertSame(0, $diff->count());

        $dummy = $this->prophesize(Dependency::class)->reveal();
        $dummyDiff = $this->prophesize(DependencyDiff::class)->reveal();
        $diff = new DependencyCollectionDiff(
            [$dummy],
            [$dummyDiff, $dummyDiff],
            [$dummy, $dummy, $dummy],
        );

        $this->assertSame(6, $diff->count());
    }
}
