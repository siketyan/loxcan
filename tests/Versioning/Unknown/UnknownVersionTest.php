<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Versioning\Unknown;

use PHPUnit\Framework\TestCase;

class UnknownVersionTest extends TestCase
{
    private const VERSION = '1.2.3.4';

    private UnknownVersion $version;

    protected function setUp(): void
    {
        $this->version = new UnknownVersion(self::VERSION);
    }

    public function testToString(): void
    {
        $this->assertSame(
            self::VERSION,
            $this->version->__toString(),
        );
    }
}
