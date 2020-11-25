<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Scanner;

use Eloquent\Pathogen\PathInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class ScannerResolverTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy $fooScanner;
    private ObjectProphecy $barScanner;
    private ScannerResolver $resolver;

    protected function setUp(): void
    {
        $this->fooScanner = $this->prophesize(ScannerInterface::class);
        $this->barScanner = $this->prophesize(ScannerInterface::class);

        $this->resolver = new ScannerResolver([
            $this->fooScanner->reveal(),
            $this->barScanner->reveal(),
        ]);
    }

    public function test(): void
    {
        $foo = $this->prophesize(PathInterface::class)->reveal();
        $bar = $this->prophesize(PathInterface::class)->reveal();
        $dummy = $this->prophesize(PathInterface::class)->reveal();

        $this->fooScanner->supports($foo)->willReturn(true);
        $this->fooScanner->supports(Argument::type(PathInterface::class))->willReturn(false);
        $this->barScanner->supports($bar)->willReturn(true);
        $this->barScanner->supports(Argument::type(PathInterface::class))->willReturn(false);

        $this->assertSame($this->fooScanner->reveal(), $this->resolver->resolve($foo));
        $this->assertSame($this->barScanner->reveal(), $this->resolver->resolve($bar));
        $this->assertNull($this->resolver->resolve($dummy));
    }
}
