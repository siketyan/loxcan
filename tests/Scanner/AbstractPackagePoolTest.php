<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Scanner;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Siketyan\Loxcan\Model\Package;

abstract class AbstractPackagePoolTest extends TestCase
{
    use ProphecyTrait;

    private AbstractPackagePool $pool;

    protected function setUp(): void
    {
        $this->pool = $this->getImpl();
    }

    abstract protected function getImpl(): AbstractPackagePool;

    public function test(): void
    {
        $name = 'dummy/dummy';

        $package = $this->prophesize(Package::class);
        $package->getName()->willReturn($name);
        $package->getConstraint()->willReturn(null);
        $package = $package->reveal();

        $this->pool->add($package);

        $this->assertSame($package, $this->pool->get($name));
        $this->assertNull($this->pool->get('not/exists'));
    }

    public function testWithConstraint(): void
    {
        $name = 'dummy/dummy';
        $constraint = '^1.2.3';

        $package = $this->prophesize(Package::class);
        $package->getName()->willReturn($name);
        $package->getConstraint()->willReturn($constraint);
        $package = $package->reveal();

        $this->pool->add($package);

        $this->assertSame($package, $this->pool->get($name, $constraint));
        $this->assertNull($this->pool->get($name), '^4.5.6');
        $this->assertNull($this->pool->get($name));
        $this->assertNull($this->pool->get('not/exists'));
    }
}
