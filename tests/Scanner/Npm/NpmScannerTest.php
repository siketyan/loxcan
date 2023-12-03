<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Scanner\Npm;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Siketyan\Loxcan\Model\DependencyCollection;
use Siketyan\Loxcan\Model\DependencyCollectionPair;
use Siketyan\Loxcan\Model\FileDiff;

class NpmScannerTest extends TestCase
{
    private MockObject&NpmLockParser $parser;
    private NpmScanner $scanner;

    protected function setUp(): void
    {
        $this->parser = $this->createMock(NpmLockParser::class);
        $this->scanner = new NpmScanner($this->parser);
    }

    public function test(): void
    {
        $beforeFile = 'dummy_before';
        $afterFile = 'dummy_after';

        $fileDiff = $this->createStub(FileDiff::class);
        $fileDiff->method('getBefore')->willReturn($beforeFile);
        $fileDiff->method('getAfter')->willReturn($afterFile);

        $before = $this->createStub(DependencyCollection::class);
        $after = $this->createStub(DependencyCollection::class);

        $this->parser->method('parse')->willReturnMap([
            [$beforeFile, $before],
            [$afterFile, $after],
        ]);

        $pair = $this->scanner->scan($fileDiff);

        $this->assertInstanceOf(DependencyCollectionPair::class, $pair);
        $this->assertSame($before, $pair->getBefore());
        $this->assertSame($after, $pair->getAfter());
    }
}
