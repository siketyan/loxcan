<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Versioning\SemVer;

use PHPUnit\Framework\TestCase;
use Siketyan\Loxcan\Versioning\Unknown\UnknownVersion;

class SemVerVersionParserTest extends TestCase
{
    private SemVerVersionParser $parser;

    protected function setUp(): void
    {
        $this->parser = new SemVerVersionParser();
    }

    public function test(): void
    {
        $version = $this->parser->parse('1.2.3-dev.1.23+build.456.alpha');
        $this->assertSame(1, $version->getMajor());
        $this->assertSame(2, $version->getMinor());
        $this->assertSame(3, $version->getPatch());
        $this->assertSame(['dev', 1, 23], $version->getPreRelease());
        $this->assertSame(['build', 456, 'alpha'], $version->getBuild());

        $version = $this->parser->parse('3.4.5-dev');
        $this->assertSame(3, $version->getMajor());
        $this->assertSame(4, $version->getMinor());
        $this->assertSame(5, $version->getPatch());
        $this->assertSame(['dev'], $version->getPreRelease());
        $this->assertSame([], $version->getBuild());

        $version = $this->parser->parse('2.3.4+abc12345');
        $this->assertSame(2, $version->getMajor());
        $this->assertSame(3, $version->getMinor());
        $this->assertSame(4, $version->getPatch());
        $this->assertSame([], $version->getPreRelease());
        $this->assertSame(['abc12345'], $version->getBuild());

        $version = $this->parser->parse('4.3.2');
        $this->assertSame(4, $version->getMajor());
        $this->assertSame(3, $version->getMinor());
        $this->assertSame(2, $version->getPatch());
        $this->assertSame([], $version->getPreRelease());
        $this->assertSame([], $version->getBuild());
    }

    public function testUnsupported(): void
    {
        $this->assertInstanceOf(
            UnknownVersion::class,
            $this->parser->parse('v1.2.3_not_supported'),
        );
    }
}
