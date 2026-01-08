<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Scanner\Go;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Siketyan\Loxcan\Model\Dependency;
use Siketyan\Loxcan\Model\Package;
use Siketyan\Loxcan\Versioning\SemVer\SemVerVersion;
use Siketyan\Loxcan\Versioning\SemVer\SemVerVersionParser;

class GoSumParserTest extends TestCase
{
    private const CONTENTS = <<<'EOS'
        github.com/foo/bar v1.2.3 h1:abc123=
        github.com/foo/bar v1.2.3/go.mod h1:def456=
        github.com/bar/baz v3.2.1 h1:ghi789=
        EOS;

    private GoPackagePool&MockObject $packagePool;
    private MockObject&SemVerVersionParser $versionParser;
    private GoSumParser $parser;

    protected function setUp(): void
    {
        $this->packagePool = $this->createMock(GoPackagePool::class);
        $this->versionParser = $this->createMock(SemVerVersionParser::class);

        $this->parser = new GoSumParser(
            $this->packagePool,
            $this->versionParser,
        );
    }

    public function test(): void
    {
        $cache = $this->createStub(Package::class);
        $fooBarVersion = $this->createStub(SemVerVersion::class);
        $barBazVersion = $this->createStub(SemVerVersion::class);

        $this->packagePool->method('get')->willReturnCallback(fn (string $name): ?Stub => match ($name) {
            'github.com/foo/bar' => null,
            'github.com/bar/baz' => $cache,
            default => $this->fail('unexpected pattern'),
        });

        $this->packagePool->expects($this->once())->method('add')->with($this->isInstanceOf(Package::class));

        $this->versionParser->method('parse')->willReturnMap([
            ['1.2.3', $fooBarVersion],
            ['3.2.1', $barBazVersion],
        ]);

        $collection = $this->parser->parse(self::CONTENTS);
        $dependencies = $collection->getDependencies();

        // Should have 2 dependencies (duplicates removed)
        $this->assertCount(2, $dependencies);
        $this->assertContainsOnlyInstancesOf(Dependency::class, $dependencies);

        $this->assertSame('github.com/foo/bar', $dependencies[0]->getPackage()->getName());
        $this->assertSame($fooBarVersion, $dependencies[0]->getVersion());

        $this->assertSame($cache, $dependencies[1]->getPackage());
        $this->assertSame($barBazVersion, $dependencies[1]->getVersion());
    }

    public function testEmptyContent(): void
    {
        $collection = $this->parser->parse(null);
        $this->assertCount(0, $collection->getDependencies());

        $collection = $this->parser->parse('');
        $this->assertCount(0, $collection->getDependencies());
    }
}
