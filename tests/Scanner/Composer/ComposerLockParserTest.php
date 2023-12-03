<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Scanner\Composer;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Siketyan\Loxcan\Model\Dependency;
use Siketyan\Loxcan\Model\Package;
use Siketyan\Loxcan\Versioning\Composer\ComposerVersion;
use Siketyan\Loxcan\Versioning\Composer\ComposerVersionParser;

class ComposerLockParserTest extends TestCase
{
    private const CONTENTS = <<<'EOS'
        {
            "packages": [
                {
                    "name": "foo/bar",
                    "version": "v1.2.3.4",
                    "dist": {
                        "reference": "hash"
                    }
                }
            ],
            "packages-dev": [
                {
                    "name": "bar/baz",
                    "version": "3.2.1",
                    "dist": {
                        "reference": "hash"
                    }
                }
            ]
        }
        EOS;

    private ComposerPackagePool&MockObject $packagePool;
    private ComposerVersionParser&MockObject $versionParser;
    private ComposerLockParser $parser;

    protected function setUp(): void
    {
        $this->packagePool = $this->createMock(ComposerPackagePool::class);
        $this->versionParser = $this->createMock(ComposerVersionParser::class);

        $this->parser = new ComposerLockParser(
            $this->packagePool,
            $this->versionParser,
        );
    }

    public function test(): void
    {
        $cache = $this->createStub(Package::class);
        $fooBarVersion = $this->createStub(ComposerVersion::class);
        $barBazVersion = $this->createStub(ComposerVersion::class);

        $this->packagePool->method('get')->willReturnCallback(fn (string $name) => match ($name) {
            'foo/bar' => null,
            'bar/baz' => $cache,
            default => $this->fail('unexpected pattern'),
        });

        $this->packagePool->expects($this->once())->method('add')->with($this->isInstanceOf(Package::class));

        $this->versionParser->method('parse')->willReturnMap([
            ['v1.2.3.4', 'hash', $fooBarVersion],
            ['3.2.1', 'hash', $barBazVersion],
        ]);

        $collection = $this->parser->parse(self::CONTENTS);
        $dependencies = $collection->getDependencies();

        $this->assertCount(2, $dependencies);
        $this->assertContainsOnlyInstancesOf(Dependency::class, $dependencies);

        $this->assertSame('foo/bar', $dependencies[0]->getPackage()->getName());
        $this->assertSame($fooBarVersion, $dependencies[0]->getVersion());

        $this->assertSame($cache, $dependencies[1]->getPackage());
        $this->assertSame($barBazVersion, $dependencies[1]->getVersion());
    }
}
