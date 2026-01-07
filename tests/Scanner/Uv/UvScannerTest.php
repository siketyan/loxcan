<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Scanner\Uv;

use Eloquent\Pathogen\Path;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Siketyan\Loxcan\Model\DependencyCollection;
use Siketyan\Loxcan\Model\DependencyCollectionPair;
use Siketyan\Loxcan\Model\FileDiff;

class UvScannerTest extends TestCase
{
    private UvLockParser&MockObject $parser;
    private UvScanner $scanner;

    protected function setUp(): void
    {
        $this->parser = $this->createMock(UvLockParser::class);
        $this->scanner = new UvScanner($this->parser);
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

    public function testSupports(): void
    {
        $this->assertTrue($this->scanner->supports(Path::fromString('/path/to/uv.lock')));
        $this->assertFalse($this->scanner->supports(Path::fromString('/path/to/Pipfile.lock')));
        $this->assertFalse($this->scanner->supports(Path::fromString('/path/to/requirements.txt')));
    }
}
