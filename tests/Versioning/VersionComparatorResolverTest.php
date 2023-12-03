<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Versioning;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class VersionComparatorResolverTest extends TestCase
{
    private MockObject&VersionComparatorInterface $fooComparator;
    private MockObject&VersionComparatorInterface $barComparator;
    private VersionComparatorResolver $resolver;

    protected function setUp(): void
    {
        $this->fooComparator = $this->createMock(VersionComparatorInterface::class);
        $this->barComparator = $this->createMock(VersionComparatorInterface::class);

        $this->resolver = new VersionComparatorResolver([
            $this->fooComparator,
            $this->barComparator,
        ]);
    }

    public function test(): void
    {
        $foo = $this->createStub(FooVersion::class);
        $bar = $this->createStub(BarVersion::class);
        $baz = $this->createStub(BazVersion::class);
        $dummy = $this->createStub(VersionInterface::class);

        $this->fooComparator
            ->method('supports')
            ->willReturnCallback(static fn (string $a, string $b): bool => match ([$a, $b]) {
                [$foo::class, $bar::class] => true,
                default => false,
            })
        ;

        $this->barComparator
            ->method('supports')
            ->willReturnCallback(static fn (string $a, string $b): bool => match ([$a, $b]) {
                [$bar::class, $baz::class] => true,
                default => false,
            })
        ;

        $this->assertSame($this->fooComparator, $this->resolver->resolve($foo, $bar));
        $this->assertSame($this->barComparator, $this->resolver->resolve($bar, $baz));
        $this->assertNull($this->resolver->resolve($dummy, $dummy));
    }
}

abstract class AbstractVersion implements VersionInterface, \Stringable
{
    public function __toString(): string
    {
        return '';
    }
}

class FooVersion extends AbstractVersion
{
}

class BarVersion extends AbstractVersion
{
}

class BazVersion extends AbstractVersion
{
}
