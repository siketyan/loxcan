<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Versioning\Simple;

use PHPUnit\Framework\TestCase;
use Siketyan\Loxcan\Versioning\Unknown\UnknownVersion;

class SimpleVersionParserTest extends TestCase
{
    private SimpleVersionParser $parser;

    protected function setUp(): void
    {
        $this->parser = new SimpleVersionParser();
    }

    public function test(): void
    {
        $version = $this->parser->parse('1.2.3.4');
        $this->assertSame(1, $version->getMajor());
        $this->assertSame(2, $version->getMinor());
        $this->assertSame(3, $version->getPatch());
        $this->assertSame(4, $version->getRevision());

        $version = $this->parser->parse('4.3.2');
        $this->assertSame(4, $version->getMajor());
        $this->assertSame(3, $version->getMinor());
        $this->assertSame(2, $version->getPatch());
        $this->assertNull($version->getRevision());
    }

    public function testUnsupported(): void
    {
        $this->assertInstanceOf(
            UnknownVersion::class,
            $this->parser->parse('v1.2.3_not_supported'),
        );
    }
}
