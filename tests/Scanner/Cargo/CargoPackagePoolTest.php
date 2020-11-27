<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Scanner\Cargo;

use Siketyan\Loxcan\Scanner\AbstractPackagePool;
use Siketyan\Loxcan\Scanner\AbstractPackagePoolTest;

class CargoPackagePoolTest extends AbstractPackagePoolTest
{
    protected function getImpl(): AbstractPackagePool
    {
        return new CargoPackagePool();
    }
}
