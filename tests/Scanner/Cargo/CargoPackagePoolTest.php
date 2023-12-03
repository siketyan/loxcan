<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Scanner\Cargo;

use JetBrains\PhpStorm\Pure;
use Siketyan\Loxcan\Scanner\AbstractPackagePool;
use Siketyan\Loxcan\Scanner\AbstractPackagePoolTestCase;

class CargoPackagePoolTest extends AbstractPackagePoolTestCase
{
    #[Pure]
    protected function getImpl(): AbstractPackagePool
    {
        return new CargoPackagePool();
    }
}
