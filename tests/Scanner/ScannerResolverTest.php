<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Scanner;

use Eloquent\Pathogen\PathInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ScannerResolverTest extends TestCase
{
    private MockObject&ScannerInterface $fooScanner;
    private MockObject&ScannerInterface $barScanner;
    private ScannerResolver $resolver;

    protected function setUp(): void
    {
        $this->fooScanner = $this->createMock(ScannerInterface::class);
        $this->barScanner = $this->createMock(ScannerInterface::class);

        $this->resolver = new ScannerResolver([
            $this->fooScanner,
            $this->barScanner,
        ]);
    }

    public function test(): void
    {
        $foo = $this->createStub(PathInterface::class);
        $bar = $this->createStub(PathInterface::class);
        $dummy = $this->createStub(PathInterface::class);

        $this->fooScanner
            ->method('supports')
            ->willReturnCallback(static fn (PathInterface $p): bool => match ($p) {
                $foo => true,
                default => false,
            })
        ;

        $this->barScanner
            ->method('supports')
            ->willReturnCallback(static fn (PathInterface $p): bool => match ($p) {
                $bar => true,
                default => false,
            })
        ;

        $this->assertSame($this->fooScanner, $this->resolver->resolve($foo));
        $this->assertSame($this->barScanner, $this->resolver->resolve($bar));
        $this->assertNull($this->resolver->resolve($dummy));
    }
}
