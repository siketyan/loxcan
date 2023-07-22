<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Versioning;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class VersionComparatorResolverTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var ObjectProphecy<VersionComparatorInterface>
     */
    private ObjectProphecy $fooComparator;

    /**
     * @var ObjectProphecy<VersionComparatorInterface>
     */
    private ObjectProphecy $barComparator;

    private VersionComparatorResolver $resolver;

    protected function setUp(): void
    {
        $this->fooComparator = $this->prophesize(VersionComparatorInterface::class);
        $this->barComparator = $this->prophesize(VersionComparatorInterface::class);

        $this->resolver = new VersionComparatorResolver([
            $this->fooComparator->reveal(),
            $this->barComparator->reveal(),
        ]);
    }

    public function test(): void
    {
        $foo = $this->prophesize(FooVersion::class)->reveal();
        $bar = $this->prophesize(BarVersion::class)->reveal();
        $baz = $this->prophesize(BazVersion::class)->reveal();
        $dummy = $this->prophesize(VersionInterface::class)->reveal();

        $this->fooComparator->supports($foo::class, $bar::class)->willReturn(true);
        $this->fooComparator->supports(Argument::type('string'), Argument::type('string'))->willReturn(false);
        $this->barComparator->supports($bar::class, $baz::class)->willReturn(true);
        $this->barComparator->supports(Argument::type('string'), Argument::type('string'))->willReturn(false);

        $this->assertSame($this->fooComparator->reveal(), $this->resolver->resolve($foo, $bar));
        $this->assertSame($this->barComparator->reveal(), $this->resolver->resolve($bar, $baz));
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
