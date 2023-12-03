<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Scanner;

use PHPUnit\Framework\TestCase;
use Siketyan\Loxcan\Model\Package;

abstract class AbstractPackagePoolTest extends TestCase
{
    private AbstractPackagePool $pool;

    protected function setUp(): void
    {
        $this->pool = $this->getImpl();
    }

    abstract protected function getImpl(): AbstractPackagePool;

    public function test(): void
    {
        $name = 'dummy/dummy';

        $package = $this->createStub(Package::class);
        $package->method('getName')->willReturn($name);
        $package->method('getConstraint')->willReturn(null);

        $this->pool->add($package);

        $this->assertSame($package, $this->pool->get($name));
        $this->assertNull($this->pool->get('not/exists'));
    }

    public function testWithConstraint(): void
    {
        $name = 'dummy/dummy';
        $constraint = '^1.2.3';

        $package = $this->createStub(Package::class);
        $package->method('getName')->willReturn($name);
        $package->method('getConstraint')->willReturn($constraint);

        $this->pool->add($package);

        $this->assertSame($package, $this->pool->get($name, $constraint));
        $this->assertNull($this->pool->get($name), '^4.5.6');
        $this->assertNull($this->pool->get($name));
        $this->assertNull($this->pool->get('not/exists'));
    }
}
