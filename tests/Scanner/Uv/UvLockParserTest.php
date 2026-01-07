<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Scanner\Uv;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Siketyan\Loxcan\Model\Dependency;
use Siketyan\Loxcan\Model\Package;
use Siketyan\Loxcan\Versioning\Simple\SimpleVersion;
use Siketyan\Loxcan\Versioning\Simple\SimpleVersionParser;

class UvLockParserTest extends TestCase
{
    private const CONTENTS = <<<'EOS'
        version = 1
        requires-python = ">=3.12"

        [[package]]
        name = "requests"
        version = "2.31.0"

        [[package]]
        name = "urllib3"
        version = "2.0.4"
        EOS;

    private UvPackagePool&MockObject $packagePool;
    private MockObject&SimpleVersionParser $versionParser;
    private UvLockParser $parser;

    protected function setUp(): void
    {
        $this->packagePool = $this->createMock(UvPackagePool::class);
        $this->versionParser = $this->createMock(SimpleVersionParser::class);

        $this->parser = new UvLockParser(
            $this->packagePool,
            $this->versionParser,
        );
    }

    public function test(): void
    {
        $cache = $this->createStub(Package::class);
        $requestsVersion = $this->createStub(SimpleVersion::class);
        $urllib3Version = $this->createStub(SimpleVersion::class);

        $this->packagePool->method('get')->willReturnCallback(fn (string $name): ?Stub => match ($name) {
            'requests' => null,
            'urllib3' => $cache,
            default => $this->fail('unexpected pattern'),
        });

        $this->packagePool->expects($this->once())->method('add')->with($this->isInstanceOf(Package::class));

        $this->versionParser->method('parse')->willReturnMap([
            ['2.31.0', $requestsVersion],
            ['2.0.4', $urllib3Version],
        ]);

        $collection = $this->parser->parse(self::CONTENTS);
        $dependencies = $collection->getDependencies();

        $this->assertCount(2, $dependencies);
        $this->assertContainsOnlyInstancesOf(Dependency::class, $dependencies);

        $this->assertSame('requests', $dependencies[0]->getPackage()->getName());
        $this->assertSame($requestsVersion, $dependencies[0]->getVersion());

        $this->assertSame($cache, $dependencies[1]->getPackage());
        $this->assertSame($urllib3Version, $dependencies[1]->getVersion());
    }

    public function testEmptyContent(): void
    {
        $collection = $this->parser->parse(null);
        $this->assertCount(0, $collection->getDependencies());

        $collection = $this->parser->parse('');
        $this->assertCount(0, $collection->getDependencies());
    }
}
