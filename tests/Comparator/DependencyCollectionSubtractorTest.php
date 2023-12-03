<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Comparator;

use PHPUnit\Framework\TestCase;
use Siketyan\Loxcan\Model\Dependency;
use Siketyan\Loxcan\Model\DependencyCollection;
use Siketyan\Loxcan\Model\Package;

class DependencyCollectionSubtractorTest extends TestCase
{
    private DependencyCollectionSubtractor $subtractor;

    protected function setUp(): void
    {
        $this->subtractor = new DependencyCollectionSubtractor();
    }

    public function test(): void
    {
        $added = $this->createStub(Dependency::class);
        $removed = $this->createStub(Dependency::class);
        $common = $this->createStub(Dependency::class);

        $added->method('getPackage')->willReturn($this->createStub(Package::class));
        $removed->method('getPackage')->willReturn($this->createStub(Package::class));
        $common->method('getPackage')->willReturn($this->createStub(Package::class));

        $a = $this->createStub(DependencyCollection::class);
        $b = $this->createStub(DependencyCollection::class);

        $a->method('getDependencies')->willReturn([$common, $removed]);
        $b->method('getDependencies')->willReturn([$common, $added]);

        $this->assertSame([$added], $this->subtractor->subtract($b, $a));
        $this->assertSame([$removed], $this->subtractor->subtract($a, $b));
    }
}
