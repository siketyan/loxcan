<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Scanner\Pip;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Siketyan\Loxcan\Model\DependencyCollection;
use Siketyan\Loxcan\Model\DependencyCollectionPair;
use Siketyan\Loxcan\Model\FileDiff;

class PipScannerTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy $parser;
    private PipScanner $scanner;

    protected function setUp(): void
    {
        $this->parser = $this->prophesize(PipLockParser::class);

        $this->scanner = new PipScanner(
            $this->parser->reveal(),
        );
    }

    public function test(): void
    {
        $beforeFile = 'dummy_before';
        $afterFile = 'dummy_after';

        $fileDiff = $this->prophesize(FileDiff::class);
        $fileDiff->getBefore()->willReturn($beforeFile);
        $fileDiff->getAfter()->willReturn($afterFile);

        $before = $this->prophesize(DependencyCollection::class)->reveal();
        $after = $this->prophesize(DependencyCollection::class)->reveal();

        $this->parser->parse($beforeFile)->willReturn($before);
        $this->parser->parse($afterFile)->willReturn($after);

        $pair = $this->scanner->scan($fileDiff->reveal());

        $this->assertInstanceOf(DependencyCollectionPair::class, $pair);
        $this->assertSame($before, $pair->getBefore());
        $this->assertSame($after, $pair->getAfter());
    }
}
